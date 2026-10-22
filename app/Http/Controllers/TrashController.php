<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\StoredFile;
use App\Services\StorageNodeService;
use App\Support\Fmt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class TrashController extends Controller
{
    public function index(Request $request)
    {
        $files = StoredFile::onlyTrashed()
            ->where('user_id', $request->user()->id)
            ->latest('deleted_at')
            ->get()
            ->map(fn (StoredFile $f) => Fmt::fileRow($f) + ['deleted' => Fmt::date($f->deleted_at)])
            ->all();

        return view('user.trash', compact('files'));
    }

    public function restore(Request $request, int $id): JsonResponse
    {
        $file = StoredFile::onlyTrashed()->where('user_id', $request->user()->id)->findOrFail($id);
        $file->restore();

        ActivityLog::record('restore', Str::before($request->user()->name, ' ') . ' memulihkan "' . $file->name . '"', ['file_id' => $file->id]);

        return response()->json(['ok' => true]);
    }

    public function purge(Request $request, StorageNodeService $nodes, int $id): JsonResponse
    {
        $file = StoredFile::onlyTrashed()->where('user_id', $request->user()->id)->findOrFail($id);
        $this->purgeFile($file, $nodes);

        return response()->json(['ok' => true]);
    }

    public function empty(Request $request, StorageNodeService $nodes): JsonResponse
    {
        $count = 0;
        StoredFile::onlyTrashed()->where('user_id', $request->user()->id)->each(function (StoredFile $file) use ($nodes, &$count) {
            $this->purgeFile($file, $nodes);
            $count++;
        });

        return response()->json(['ok' => true, 'count' => $count]);
    }

    /** Hapus permanen: buang dari semua node, lalu hapus baris (replika & tautan ikut terhapus). */
    private function purgeFile(StoredFile $file, StorageNodeService $nodes): void
    {
        foreach ($file->replicas()->with('node')->get() as $replica) {
            try {
                $nodes->disk($replica->node)->delete($file->stored_path);
            } catch (Throwable $e) {
                report($e);
            }
        }

        ActivityLog::record('delete', 'File "' . $file->name . '" dihapus permanen', ['file_id' => $file->id], $file->user_id);
        $file->forceDelete();
    }
}
