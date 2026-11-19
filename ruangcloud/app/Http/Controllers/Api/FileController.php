<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FileController as WebFileController;
use App\Models\Folder;
use App\Models\StoredFile;
use App\Services\StorageNodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * REST API file. Daftar dan detail dijawab langsung di sini;
 * ubah nama, pindah, hapus, dan unduh didelegasikan ke controller web
 * agar validasi, hak akses, dan log aktivitasnya selalu sama.
 */
class FileController extends Controller
{
    /** Daftar file milik pengguna. Filter: ?folder_id= (kosong = folder utama) atau ?q= untuk pencarian. */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = StoredFile::where('user_id', $user->id);

        if ($request->filled('q')) {
            $like = '%' . addcslashes((string) $request->query('q'), "%_\\") . '%';
            $query->where('name', 'like', $like);
        } else {
            $folderId = $request->query('folder_id');
            if ($folderId !== null && $folderId !== '') {
                $folderId = Folder::where('user_id', $user->id)->whereKey($folderId)->value('id');
                if (! $folderId) {
                    return response()->json(['message' => 'Folder tidak valid.'], 422);
                }
            } else {
                $folderId = null;
            }
            $query->where('folder_id', $folderId);
        }

        $files = $query->latest()->paginate(50);

        return response()->json([
            'data' => collect($files->items())->map(fn (StoredFile $f) => $this->fileData($f))->all(),
            'meta' => [
                'current_page' => $files->currentPage(),
                'last_page'    => $files->lastPage(),
                'total'        => $files->total(),
            ],
        ]);
    }

    public function show(Request $request, StoredFile $file): JsonResponse
    {
        $this->authorizeFile($request, $file);

        return response()->json(['data' => $this->fileData($file)]);
    }

    public function rename(Request $request, StoredFile $file): JsonResponse
    {
        return app(WebFileController::class)->rename($request, $file);
    }

    public function move(Request $request, StoredFile $file): JsonResponse
    {
        return app(WebFileController::class)->move($request, $file);
    }

    public function destroy(Request $request, StoredFile $file): JsonResponse
    {
        return app(WebFileController::class)->destroy($request, $file);
    }

    public function download(Request $request, StoredFile $file)
    {
        return app(WebFileController::class)->download($request, $file, app(StorageNodeService::class));
    }

    private function fileData(StoredFile $file): array
    {
        return [
            'id'         => $file->id,
            'name'       => $file->name,
            'mime'       => $file->mime,
            'size'       => (int) $file->size,
            'folder_id'  => $file->folder_id,
            'created_at' => $file->created_at?->toISOString(),
            'updated_at' => $file->updated_at?->toISOString(),
        ];
    }

    private function authorizeFile(Request $request, StoredFile $file): void
    {
        $user = $request->user();
        abort_unless($file->user_id === $user->id || $user->role === 'admin', 403);
    }
}
