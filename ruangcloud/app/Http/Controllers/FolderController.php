<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Folder;
use App\Services\FolderArchiveService;
use App\Support\UniqueName;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FolderController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:100'],
            'parent_id' => ['nullable', 'integer'],
        ]);

        $parentId = null;
        if (! empty($data['parent_id'])) {
            $parentId = Folder::where('user_id', $request->user()->id)->whereKey($data['parent_id'])->value('id');
            if (! $parentId) {
                return response()->json(['message' => 'Folder induk tidak valid.'], 422);
            }
        }

        // Nama folder sama di induk yang sama -> beri penanda " (1)", " (2)", dst.
        $name = UniqueName::resolve(
            trim($data['name']),
            fn(string $candidate) => Folder::where('user_id', $request->user()->id)
                ->where('parent_id', $parentId)
                ->where('name', $candidate)
                ->exists()
        );

        $folder = Folder::create(['user_id' => $request->user()->id, 'parent_id' => $parentId, 'name' => $name]);

        ActivityLog::record('folder', Str::before($request->user()->name, ' ') . ' membuat folder "' . $folder->name . '"');

        return response()->json(['id' => $folder->id, 'name' => $folder->name]);
    }

    public function rename(Request $request, Folder $folder): JsonResponse
    {
        abort_unless($folder->user_id === $request->user()->id, 403);
        $data = $request->validate(['name' => ['required', 'string', 'max:100']]);

        $name = UniqueName::resolve(
            trim($data['name']),
            fn(string $candidate) => Folder::where('user_id', $request->user()->id)
                ->where('parent_id', $folder->parent_id)
                ->where('name', $candidate)
                ->whereKeyNot($folder->id)
                ->exists()
        );

        $folder->update(['name' => $name]);

        return response()->json(['ok' => true, 'name' => $name]);
    }

    /** Folder hanya bisa dihapus bila kosong. */
    public function destroy(Request $request, Folder $folder): JsonResponse
    {
        abort_unless($folder->user_id === $request->user()->id, 403);

        if ($folder->files()->exists() || $folder->children()->exists()) {
            return response()->json(['message' => 'Folder masih berisi file atau subfolder. Kosongkan dulu.'], 422);
        }

        $folder->delete();

        return response()->json(['ok' => true]);
    }

    /** Unduh seluruh isi folder sebagai satu file ZIP. */
    public function download(Request $request, Folder $folder, FolderArchiveService $archive)
    {
        abort_unless($folder->user_id === $request->user()->id, 403);

        [$zipPath, $count] = $archive->build($folder);
        abort_if($count === 0, 404, 'Folder ini belum berisi file yang bisa diunduh.');

        ActivityLog::record('download', Str::before($request->user()->name, ' ') . ' mengunduh folder "' . $folder->name . '" sebagai ZIP');

        $zipName = trim(preg_replace('/[^A-Za-z0-9]+/', '-', $folder->name), '-');
        $zipName = ($zipName !== '' ? $zipName : 'folder') . '.zip';

        return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
    }

    /** Pindahkan folder ke folder induk lain (atau ke folder utama). */
    public function move(Request $request, Folder $folder): JsonResponse
    {
        abort_unless($folder->user_id === $request->user()->id, 403);
        $data = $request->validate(['parent_id' => ['nullable', 'integer']]);

        $parentId = null;
        if (! empty($data['parent_id'])) {
            $parentId = Folder::where('user_id', $request->user()->id)->whereKey($data['parent_id'])->value('id');
            if (! $parentId) {
                return response()->json(['message' => 'Folder tujuan tidak valid.'], 422);
            }
        }

        if ($parentId === $folder->id) {
            return response()->json(['message' => 'Folder tidak bisa dipindahkan ke dirinya sendiri.'], 422);
        }

        if ($parentId !== null && in_array($parentId, $folder->descendantIds(), true)) {
            return response()->json(['message' => 'Folder tidak bisa dipindahkan ke dalam subfoldernya sendiri.'], 422);
        }

        $name = UniqueName::resolve(
            $folder->name,
            fn(string $candidate) => Folder::where('user_id', $request->user()->id)
                ->where('parent_id', $parentId)
                ->where('name', $candidate)
                ->whereKeyNot($folder->id)
                ->exists()
        );

        $folder->update(['parent_id' => $parentId, 'name' => $name]);

        ActivityLog::record('folder', Str::before($request->user()->name, ' ') . ' memindahkan folder "' . $name . '"');

        return response()->json(['ok' => true, 'name' => $name]);
    }
}
