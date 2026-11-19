<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FolderController as WebFolderController;
use App\Models\Folder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * REST API folder. Daftar dijawab langsung di sini;
 * buat, ubah nama, pindah, dan hapus didelegasikan ke controller web
 * agar validasi, penamaan unik, dan penjaga siklusnya selalu sama.
 */
class FolderController extends Controller
{
    /** Daftar folder milik pengguna. Filter: ?parent_id= (kosong = folder utama). */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $parentId = $request->query('parent_id');
        if ($parentId !== null && $parentId !== '') {
            $parentId = Folder::where('user_id', $user->id)->whereKey($parentId)->value('id');
            if (! $parentId) {
                return response()->json(['message' => 'Folder induk tidak valid.'], 422);
            }
        } else {
            $parentId = null;
        }

        $folders = Folder::where('user_id', $user->id)
            ->where('parent_id', $parentId)
            ->withCount('files')
            ->orderBy('name')
            ->get()
            ->map(fn (Folder $f) => [
                'id'         => $f->id,
                'name'       => $f->name,
                'parent_id'  => $f->parent_id,
                'files'      => $f->files_count,
                'created_at' => $f->created_at?->toISOString(),
            ])
            ->all();

        return response()->json(['data' => $folders]);
    }

    public function store(Request $request): JsonResponse
    {
        return app(WebFolderController::class)->store($request);
    }

    public function rename(Request $request, Folder $folder): JsonResponse
    {
        return app(WebFolderController::class)->rename($request, $folder);
    }

    public function move(Request $request, Folder $folder): JsonResponse
    {
        return app(WebFolderController::class)->move($request, $folder);
    }

    public function destroy(Request $request, Folder $folder): JsonResponse
    {
        return app(WebFolderController::class)->destroy($request, $folder);
    }
}
