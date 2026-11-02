<?php

namespace App\Http\Controllers;

use App\Models\DownloadRecord;
use App\Models\StoredFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Gerbang unduhan selesai: satu file hanya bisa diunduh sekali per pengguna.
 * Catatan dibuat saat unduhan benar-benar selesai; menghapus catatan
 * membuka kembali izin mengunduh file itu.
 */
class DownloadController extends Controller
{
    public function check(Request $request): JsonResponse
    {
        $data = $request->validate(['file_id' => ['required', 'integer']]);

        $downloaded = DownloadRecord::where('user_id', $request->user()->id)
            ->where('file_id', $data['file_id'])
            ->exists();

        return response()->json(['downloaded' => $downloaded]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate(['file_id' => ['required', 'integer']]);

        $file = StoredFile::where('user_id', $request->user()->id)->findOrFail($data['file_id']);

        DownloadRecord::firstOrCreate(
            ['user_id' => $request->user()->id, 'file_id' => $file->id],
            ['size' => $file->size],
        );

        return response()->json(['ok' => true]);
    }

    public function destroy(Request $request, DownloadRecord $record): JsonResponse
    {
        abort_unless($record->user_id === $request->user()->id, 403);

        $record->delete();

        return response()->json(['ok' => true]);
    }
}
