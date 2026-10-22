<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Folder;
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

        $folder = Folder::create(['user_id' => $request->user()->id, 'parent_id' => $parentId, 'name' => trim($data['name'])]);

        ActivityLog::record('folder', Str::before($request->user()->name, ' ') . ' membuat folder "' . $folder->name . '"');

        return response()->json(['id' => $folder->id, 'name' => $folder->name]);
    }

    public function rename(Request $request, Folder $folder): JsonResponse
    {
        abort_unless($folder->user_id === $request->user()->id, 403);
        $data = $request->validate(['name' => ['required', 'string', 'max:100']]);

        $folder->update(['name' => trim($data['name'])]);

        return response()->json(['ok' => true]);
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
}
