<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\FileReplica;
use App\Models\StorageNode;
use App\Models\StoredFile;
use App\Models\UploadSession;
use App\Models\User;
use App\Support\Fmt;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Unduhan laporan Admin Console dalam format CSV (dapat dibuka di Excel):
 * laporan sistem (tombol "Unduh laporan" di panel Ringkasan) dan laporan
 * penyimpanan (tombol "Ekspor Laporan" di panel Penyimpanan).
 * Kedua route dijaga middleware EnsureRole:admin.
 */
class AdminReportController extends Controller
{
    /** Samarkan nama berkas di teks aktivitas, sama seperti AdminController. */
    private function anonymizeFileNames(string $text): string
    {
        return preg_replace_callback('/"([^"]+)"/', function (array $m) {
            if (preg_match('/\.([A-Za-z0-9]{2,5})$/', $m[1], $e)) {
                return 'berkas ' . strtoupper($e[1]);
            }

            return $m[0];
        }, $text);
    }

    /** Bungkus penulis CSV menjadi respons unduhan (BOM UTF-8 agar rapi di Excel). */
    private function download(string $filename, callable $writer): StreamedResponse
    {
        return response()->streamDownload(function () use ($writer) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            $writer($out);
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** Tulis satu baris CSV (pemisah ";" agar rapi di Excel lokal Indonesia). */
    private function row($out, array $cols): void
    {
        fputcsv($out, $cols, ';');
    }

    /** Total byte fisik terpakai (hanya replika synced, sama seperti AdminController). */
    private function usedBytesAll(): int
    {
        return (int) FileReplica::where('file_replicas.status', 'synced')
            ->join('files', 'files.id', '=', 'file_replicas.file_id')
            ->sum('files.size');
    }

    public function sistem(Request $request): StreamedResponse
    {
        $nodes         = StorageNode::orderBy('id')->get();
        $usedAll       = $this->usedBytesAll();
        $capacityAll   = (int) $nodes->sum('capacity_bytes');
        $activeUploads = UploadSession::whereIn('status', ['uploading', 'assembling'])->count();
        $nodesActive   = $nodes->where('status', 'active')->count();

        $usedPerUser = StoredFile::withTrashed()
            ->selectRaw('user_id, SUM(size) as total')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');
        $countPerUser = StoredFile::withoutTrashed()
            ->selectRaw('user_id, COUNT(*) as total')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');
        $users = User::orderBy('name')->get();

        $activities = ActivityLog::with('user')->latest()->take(25)->get();
        $generated  = now()->locale('id')->isoFormat('D MMMM YYYY HH:mm') . ' WIB';

        return $this->download('laporan-sistem-' . now()->format('Y-m-d') . '.csv',
            function ($out) use ($nodes, $usedAll, $capacityAll, $activeUploads, $nodesActive, $usedPerUser, $countPerUser, $users, $activities, $generated) {
                $this->row($out, ['LAPORAN SISTEM RUANGCLOUD']);
                $this->row($out, ['Dibuat pada', $generated]);
                $this->row($out, []);

                $this->row($out, ['RINGKASAN']);
                $this->row($out, ['Total pengguna', $users->count()]);
                $this->row($out, ['Kapasitas terpakai', Fmt::bytes($usedAll) . ' dari ' . Fmt::bytes($capacityAll)]);
                $this->row($out, ['Unggahan aktif', $activeUploads]);
                $this->row($out, ['Node aktif', $nodesActive . ' dari ' . $nodes->count()]);
                $this->row($out, []);

                $this->row($out, ['PENGGUNA']);
                $this->row($out, ['Nama', 'Email', 'Peran', 'Status', 'Kuota', 'Terpakai', 'Jumlah file', 'Terdaftar']);
                foreach ($users as $u) {
                    $this->row($out, [
                        $u->name,
                        $u->email,
                        $u->role,
                        $u->is_active ? 'Aktif' : 'Nonaktif',
                        Fmt::bytes((int) $u->quota_bytes),
                        Fmt::bytes((int) ($usedPerUser[$u->id] ?? 0)),
                        (int) ($countPerUser[$u->id] ?? 0),
                        $u->created_at?->format('d/m/Y'),
                    ]);
                }
                $this->row($out, []);

                $this->row($out, ['NODE PENYIMPANAN']);
                $this->row($out, ['Node', 'Kode', 'Status', 'Kapasitas', 'Terpakai', 'Pemakaian']);
                foreach ($nodes as $n) {
                    $this->row($out, [
                        $n->name,
                        $n->code,
                        $n->status,
                        Fmt::bytes((int) $n->capacity_bytes),
                        Fmt::bytes($n->usedBytes()),
                        $n->usagePercent() . '%',
                    ]);
                }
                $this->row($out, []);

                $this->row($out, ['AKTIVITAS TERAKHIR']);
                $this->row($out, ['Waktu', 'Pengguna', 'Aksi', 'Deskripsi']);
                foreach ($activities as $a) {
                    $this->row($out, [
                        $a->created_at?->format('d/m/Y H:i'),
                        $a->user?->name ?? '-',
                        $a->action,
                        $this->anonymizeFileNames($a->description),
                    ]);
                }
            });
    }

    public function penyimpanan(Request $request): StreamedResponse
    {
        $nodes       = StorageNode::orderBy('id')->get();
        $usedAll     = $this->usedBytesAll();
        $capacityAll = (int) $nodes->sum('capacity_bytes');
        $fileCount   = StoredFile::withoutTrashed()->count();
        $generated   = now()->locale('id')->isoFormat('D MMMM YYYY HH:mm') . ' WIB';

        $files = StoredFile::withoutTrashed()
            ->with(['user', 'folder', 'replicas.node'])
            ->latest()
            ->take(200)
            ->get();

        return $this->download('laporan-penyimpanan-' . now()->format('Y-m-d') . '.csv',
            function ($out) use ($nodes, $usedAll, $capacityAll, $fileCount, $generated, $files) {
                $this->row($out, ['LAPORAN PENYIMPANAN RUANGCLOUD']);
                $this->row($out, ['Dibuat pada', $generated]);
                $this->row($out, []);

                $this->row($out, ['RINGKASAN CLUSTER']);
                $this->row($out, ['Total kapasitas', Fmt::bytes($capacityAll)]);
                $this->row($out, ['Terpakai (fisik, termasuk replika)', Fmt::bytes($usedAll)]);
                $this->row($out, ['Jumlah file aktif', $fileCount]);
                $this->row($out, []);

                $this->row($out, ['NODE PENYIMPANAN']);
                $this->row($out, ['Node', 'Kode', 'Status', 'Kapasitas', 'Terpakai', 'Bebas', 'Pemakaian', 'File tersimpan', 'Total replika']);
                foreach ($nodes as $n) {
                    $this->row($out, [
                        $n->name,
                        $n->code,
                        $n->status,
                        Fmt::bytes((int) $n->capacity_bytes),
                        Fmt::bytes($n->usedBytes()),
                        Fmt::bytes($n->freeBytes()),
                        $n->usagePercent() . '%',
                        FileReplica::where('storage_node_id', $n->id)->where('status', 'synced')->count(),
                        FileReplica::where('storage_node_id', $n->id)->count(),
                    ]);
                }
                $this->row($out, []);

                $this->row($out, ['DAFTAR FILE (maks. 200 terbaru)']);
                $this->row($out, ['Nama file', 'Pemilik', 'Folder', 'Ukuran', 'Node primer', 'Replika synced', 'Diunggah']);
                foreach ($files as $f) {
                    $primary = $f->replicas->firstWhere('is_primary', true);
                    $this->row($out, [
                        $f->name,
                        $f->user?->name ?? '-',
                        $f->folder?->name ?? '(root)',
                        Fmt::bytes((int) $f->size),
                        $primary?->node?->name ?? '-',
                        $f->replicas->where('status', 'synced')->count() . ' dari ' . $f->replicas->count(),
                        $f->created_at?->format('d/m/Y H:i'),
                    ]);
                }
            });
    }

    public function audit(Request $request): StreamedResponse
    {
        $logs      = ActivityLog::with('user')->latest()->take(500)->get();
        $generated = now()->locale('id')->isoFormat('D MMMM YYYY HH:mm') . ' WIB';

        return $this->download('laporan-audit-log-' . now()->format('Y-m-d') . '.csv',
            function ($out) use ($logs, $generated) {
                $this->row($out, ['LAPORAN AUDIT LOG RUANGCLOUD']);
                $this->row($out, ['Dibuat pada', $generated]);
                $this->row($out, []);
                $this->row($out, ['Waktu', 'Pengguna', 'Email', 'Aksi', 'Deskripsi']);
                foreach ($logs as $log) {
                    $this->row($out, [
                        $log->created_at?->format('d/m/Y H:i'),
                        $log->user?->name ?? '-',
                        $log->user?->email ?? '-',
                        $log->action,
                        $this->anonymizeFileNames($log->description),
                    ]);
                }
            });
    }
}
