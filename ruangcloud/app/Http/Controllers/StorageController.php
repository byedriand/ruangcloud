<?php

namespace App\Http\Controllers;

use App\Models\FileReplica;
use App\Models\Folder;
use App\Models\StorageNode;
use App\Models\StoredFile;
use App\Support\Fmt;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Halaman detail penyimpanan milik pengguna yang sedang masuk. */
class StorageController extends Controller
{
    /** Definisi kategori berkas: [label, ikon, kelas gradasi bar]. */
    private const CATEGORIES = [
        'gambar'      => ['Foto & Gambar', 'image', 'from-violet-500 to-purple-400'],
        'video'       => ['Video', 'video', 'from-sky-500 to-blue-400'],
        'audio'       => ['Audio & Musik', 'music', 'from-fuchsia-500 to-pink-400'],
        'dokumen'     => ['Dokumen', 'file', 'from-emerald-500 to-teal-400'],
        'spreadsheet' => ['Spreadsheet', 'file', 'from-green-500 to-emerald-400'],
        'presentasi'  => ['Presentasi', 'archive', 'from-orange-500 to-amber-400'],
        'arsip'       => ['Arsip', 'archive', 'from-amber-500 to-orange-400'],
        'lainnya'     => ['Lainnya', 'file', 'from-slate-500 to-slate-400'],
    ];

    public function index(Request $request): View
    {
        $user  = $request->user();
        $quota = (int) $user->quota_bytes;

        // ── Ringkasan (selaras sidebar: file di sampah tetap dihitung) ──
        $files      = StoredFile::where('user_id', $user->id)->get(['id', 'folder_id', 'name', 'mime', 'size', 'created_at']);
        $usedActive = (int) $files->sum('size');
        $trashBytes = (int) StoredFile::onlyTrashed()->where('user_id', $user->id)->sum('size');
        $trashCount = (int) StoredFile::onlyTrashed()->where('user_id', $user->id)->count();
        $used       = $usedActive + $trashBytes;
        $remaining  = max(0, $quota - $used);
        $percent    = $quota > 0 ? (int) min(100, round($used / $quota * 100)) : 0;

        // ── Rincian per jenis berkas (file aktif saja) ──
        $grouped = $files->groupBy(fn (StoredFile $f) => $this->categoryOf($f));
        $categories = [];
        foreach (self::CATEGORIES as $key => [$label, $icon, $bar]) {
            $group = $grouped->get($key);
            if (! $group) {
                continue;
            }
            $bytes = (int) $group->sum('size');
            $categories[] = [
                'label'   => $label,
                'icon'    => $icon,
                'bar'     => $bar,
                'bytes'   => $bytes,
                'size'    => Fmt::bytes($bytes),
                'count'   => $group->count(),
                'percent' => $usedActive > 0 ? (int) round($bytes / $usedActive * 100) : 0,
            ];
        }
        usort($categories, fn ($a, $b) => $b['bytes'] <=> $a['bytes']);

        // ── Rincian per node (salinan fisik tersinkron dari file aktif) ──
        $nodes = StorageNode::orderBy('id')->get()->map(function (StorageNode $node) use ($user) {
            $row = FileReplica::query()
                ->where('file_replicas.storage_node_id', $node->id)
                ->where('file_replicas.status', 'synced')
                ->join('files', 'files.id', '=', 'file_replicas.file_id')
                ->where('files.user_id', $user->id)
                ->whereNull('files.deleted_at')
                ->selectRaw('COALESCE(SUM(files.size), 0) AS total, COUNT(DISTINCT files.id) AS cnt')
                ->first();

            return [
                'name'  => $node->name,
                'bytes' => (int) ($row->total ?? 0),
                'size'  => Fmt::bytes((int) ($row->total ?? 0)),
                'count' => (int) ($row->cnt ?? 0),
            ];
        });
        $nodeMax = max(1, (int) $nodes->max('bytes'));

        // ── File terbesar ──
        $folderNames = Folder::where('user_id', $user->id)->pluck('name', 'id');
        $largest = $files->sortByDesc('size')->take(8)->map(function (StoredFile $f) use ($folderNames) {
            [$type, $icon] = Fmt::meta($f->name, $f->mime);

            return [
                'name'   => $f->name,
                'type'   => $type,
                'icon'   => $icon,
                'size'   => Fmt::bytes((int) $f->size),
                'folder' => $f->folder_id ? ($folderNames[$f->folder_id] ?? 'Folder') : 'File saya',
                'date'   => Fmt::date($f->created_at),
            ];
        })->values();

        return view('user.storage', [
            'summary' => [
                'used'         => Fmt::bytes($used),
                'used_active'  => Fmt::bytes($usedActive),
                'quota'        => Fmt::bytes($quota),
                'remaining'    => Fmt::bytes($remaining),
                'percent'      => $percent,
                'files'        => $files->count(),
                'trash_bytes'  => Fmt::bytes($trashBytes),
                'trash_count'  => $trashCount,
                'trash_bytes_raw' => $trashBytes,
            ],
            'categories' => $categories,
            'nodes'      => $nodes,
            'nodeMax'    => $nodeMax,
            'largest'    => $largest,
        ]);
    }

    /** Kelompokkan berkas ke kategori tampilan berdasarkan ekstensi & mime. */
    private function categoryOf(StoredFile $file): string
    {
        $ext  = Fmt::extension($file->name, $file->mime);
        $mime = (string) $file->mime;

        return match (true) {
            str_starts_with($mime, 'image/') || in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']) => 'gambar',
            str_starts_with($mime, 'video/') || in_array($ext, ['mp4', 'mov', 'mkv', 'avi', 'webm'])          => 'video',
            str_starts_with($mime, 'audio/') || in_array($ext, ['mp3', 'wav', 'ogg', 'flac'])                 => 'audio',
            in_array($ext, ['xls', 'xlsx', 'csv'])                                                             => 'spreadsheet',
            in_array($ext, ['ppt', 'pptx'])                                                                    => 'presentasi',
            in_array($ext, ['zip', 'rar', '7z', 'tar', 'gz'])                                                  => 'arsip',
            in_array($ext, ['pdf', 'doc', 'docx', 'txt', 'md']) || str_starts_with($mime, 'text/')             => 'dokumen',
            default                                                                                            => 'lainnya',
        };
    }
}
