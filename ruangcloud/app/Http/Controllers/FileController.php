<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Folder;
use App\Models\StoredFile;
use App\Services\StorageNodeService;
use App\Support\Fmt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FileController extends Controller
{
    /** File saya: isi folder aktif (atau hasil pencarian ?q=). */
    public function index(Request $request)
    {
        $user = $request->user();
        $q = trim((string) $request->query('q', ''));

        $folder = null;
        $crumbs = [];
        if ($q === '' && $request->filled('folder')) {
            $folder = Folder::where('user_id', $user->id)->findOrFail($request->query('folder'));

            $cursor = $folder;
            while ($cursor && count($crumbs) < 10) {
                array_unshift($crumbs, $cursor);
                $cursor = $cursor->parent_id ? Folder::where('user_id', $user->id)->find($cursor->parent_id) : null;
            }
        }

        $filesQuery = StoredFile::where('user_id', $user->id);
        $foldersQuery = Folder::where('user_id', $user->id)->withCount('files');

        if ($q !== '') {
            $like = '%' . addcslashes($q, '%_\\') . '%';
            $filesQuery->where('name', 'like', $like);
            $foldersQuery->where('name', 'like', $like);
        } else {
            $filesQuery->where('folder_id', $folder?->id);
            $foldersQuery->where('parent_id', $folder?->id);
        }

        $paginator = $filesQuery->latest()->paginate(20)->withQueryString();
        $rows = $paginator->getCollection()->map(fn (StoredFile $f) => Fmt::fileRow($f))->all();

        $tones = ['mint', 'lavender', 'amber', 'blue'];
        $folders = $foldersQuery->orderBy('name')->get()->values()->map(fn (Folder $f, int $i) => [
            'id'    => $f->id,
            'name'  => $f->name,
            'files' => $f->files_count . ' file',
            'tone'  => $tones[$i % count($tones)],
        ])->all();

        return view('user.files', compact('folder', 'crumbs', 'folders', 'paginator', 'rows', 'q'));
    }

    /** Terbaru: 50 file terakhir dari semua folder. */
    public function recent(Request $request)
    {
        $rows = StoredFile::where('user_id', $request->user()->id)
            ->latest()->take(50)->get()
            ->map(fn (StoredFile $f) => Fmt::fileRow($f))->all();

        return view('user.recent', compact('rows'));
    }

    public function download(Request $request, StoredFile $file, StorageNodeService $nodes)
    {
        $this->authorizeFile($request, $file);

        $response = $nodes->downloadResponse($file);
        abort_if($response === null, 503, 'File sementara tidak tersedia: semua replika tidak dapat diakses.');

        ActivityLog::record('download', Str::before($request->user()->name, ' ') . ' mengunduh "' . $file->name . '"', ['file_id' => $file->id]);

        return $response;
    }

    public function preview(Request $request, StoredFile $file, StorageNodeService $nodes)
    {
        $this->authorizeFile($request, $file);

        $replicas = $file->replicas()->with('node')
            ->where('status', 'synced')
            ->orderByDesc('is_primary')
            ->get();

        foreach ($replicas as $replica) {
            if ($replica->node->status !== 'active') continue;

            $disk = $nodes->disk($replica->node);
            if (! $disk->exists($file->stored_path)) continue;

            $mime = $file->mime ?: 'application/octet-stream';

            return $disk->response($file->stored_path, $file->name, [
                'Content-Type'        => $mime,
                'Content-Disposition' => 'inline; filename="' . addslashes($file->name) . '"',
                'Cache-Control'       => 'private, max-age=300',
            ]);
        }

        abort(503, 'File sementara tidak tersedia.');
    }

    public function rename(Request $request, StoredFile $file): JsonResponse
    {
        $this->authorizeFile($request, $file);
        $data = $request->validate(['name' => ['required', 'string', 'max:255']]);

        $rawName = trim($data['name']);

        // Deteksi ekstensi asli file (dari nama file sebelumnya atau fallback MIME)
        $origExt = Fmt::extension($file->name, $file->mime);

        if ($origExt !== '') {
            $dotExt = '.' . $origExt;
            // Jika nama baru belum memiliki ekstensi aslinya, tambahkan otomatis
            if (! str_ends_with(strtolower($rawName), strtolower($dotExt))) {
                $rawName = rtrim($rawName, '.') . $dotExt;
            }
        }

        $file->update(['name' => $rawName]);

        ActivityLog::record(
            'file',
            \Illuminate\Support\Str::before($request->user()->name, ' ') . ' mengubah nama file menjadi "' . $rawName . '"',
            ['file_id' => $file->id]
        );

        return response()->json(['ok' => true, 'name' => $rawName]);
    }

    public function move(Request $request, StoredFile $file): JsonResponse
    {
        $this->authorizeFile($request, $file);
        $data = $request->validate(['folder_id' => ['nullable', 'integer']]);

        $folderId = null;
        if (! empty($data['folder_id'])) {
            $folderId = Folder::where('user_id', $request->user()->id)->whereKey($data['folder_id'])->value('id');
            if (! $folderId) {
                return response()->json(['message' => 'Folder tujuan tidak valid.'], 422);
            }
        }

        $file->update(['folder_id' => $folderId]);

        return response()->json(['ok' => true]);
    }

    /** Hapus = pindah ke Sampah (soft delete). Penghapusan permanen ada di TrashController. */
    public function destroy(Request $request, StoredFile $file): JsonResponse
    {
        $this->authorizeFile($request, $file);

        ActivityLog::record('delete', Str::before($request->user()->name, ' ') . ' memindahkan "' . $file->name . '" ke sampah', ['file_id' => $file->id]);
        $file->delete();

        return response()->json(['ok' => true]);
    }

    private function authorizeFile(Request $request, StoredFile $file): void
    {
        $user = $request->user();
        abort_unless($file->user_id === $user->id || $user->role === 'admin', 403);
    }
}
