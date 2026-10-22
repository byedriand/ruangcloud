<?php

namespace App\Http\Controllers;

use App\Jobs\AssembleUpload;
use App\Models\Folder;
use App\Models\StoredFile;
use App\Models\UploadSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Resumable chunk upload (KAK 5.2):
 *   init -> chunk (berulang) -> complete -> (queue) AssembleUpload -> ReplicateFile
 */
class UploadController extends Controller
{
    public function init(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'size'        => ['required', 'integer', 'min:1', 'max:' . config('nodes.max_file_size')],
            'mime'        => ['nullable', 'string', 'max:150'],
            'fingerprint' => ['required', 'string', 'size:64', 'regex:/^[a-f0-9]+$/'],
            'folder_id'   => ['nullable', 'integer'],
        ]);

        $user = $request->user();

        $folderId = null;
        if (! empty($data['folder_id'])) {
            $folderId = Folder::where('user_id', $user->id)->whereKey($data['folder_id'])->value('id');
            if (! $folderId) {
                return response()->json(['message' => 'Folder tidak valid.'], 422);
            }
        }

        // Lanjutkan sesi lama untuk file yang sama (fingerprint) bila belum kedaluwarsa.
        $session = UploadSession::where('user_id', $user->id)
            ->where('fingerprint', $data['fingerprint'])
            ->where('status', 'uploading')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
        $resumed = (bool) $session;

        if (! $session) {
            $used = (int) StoredFile::withTrashed()->where('user_id', $user->id)->sum('size');
            if ($used + $data['size'] > $user->quota_bytes) {
                return response()->json(['message' => 'Kuota penyimpanan tidak cukup.'], 422);
            }

            $chunkSize = max(1024 * 1024, (int) config('nodes.chunk_size'));
            $session = UploadSession::create([
                'user_id'      => $user->id,
                'folder_id'    => $folderId,
                'file_name'    => $data['name'],
                'mime'         => $data['mime'] ?? null,
                'file_size'    => $data['size'],
                'fingerprint'  => $data['fingerprint'],
                'chunk_size'   => $chunkSize,
                'total_chunks' => (int) ceil($data['size'] / $chunkSize),
                'expires_at'   => now()->addHours((int) config('nodes.session_ttl_hours')),
            ]);
        }

        return response()->json([
            'session_id'   => $session->id,
            'chunk_size'   => $session->chunk_size,
            'total_chunks' => $session->total_chunks,
            'received'     => $this->received($session),
            'resumed'      => $resumed,
        ]);
    }

    public function status(Request $request, UploadSession $session): JsonResponse
    {
        $this->authorizeSession($request, $session);

        return response()->json([
            'status'       => $session->status,
            'error'        => $session->error,
            'total_chunks' => $session->total_chunks,
            'received'     => $session->status === 'uploading' ? $this->received($session) : [],
            'file_id'      => $session->file_id,
        ]);
    }

    public function chunk(Request $request, UploadSession $session): JsonResponse
    {
        $this->authorizeSession($request, $session);

        if ($session->status !== 'uploading') {
            return response()->json(['message' => 'Sesi unggah sudah tidak aktif.'], 409);
        }
        if ($session->expires_at->isPast()) {
            return response()->json(['message' => 'Sesi unggah kedaluwarsa. Mulai ulang unggahan.'], 410);
        }

        $data = $request->validate([
            'index'    => ['required', 'integer', 'min:0', 'max:' . ($session->total_chunks - 1)],
            'checksum' => ['required', 'string', 'size:64'],
            'chunk'    => ['required', 'file', 'max:' . (int) ceil($session->chunk_size / 1024)],
        ]);

        $index = (int) $data['index'];
        $file  = $request->file('chunk');

        if ($file->getSize() !== $session->expectedSize($index)) {
            return response()->json(['message' => 'Ukuran chunk tidak sesuai.'], 422);
        }
        if (! hash_equals(strtolower($data['checksum']), hash_file('sha256', $file->getRealPath()))) {
            return response()->json(['message' => 'Checksum chunk tidak cocok.'], 422);
        }

        Storage::disk('local')->putFileAs($session->chunkDir(), $file, UploadSession::chunkName($index));

        $count = count($this->received($session));
        $session->update(['received_count' => $count, 'last_chunk_at' => now()]);

        return response()->json(['received_count' => $count, 'total_chunks' => $session->total_chunks]);
    }

    public function complete(Request $request, UploadSession $session): JsonResponse
    {
        $this->authorizeSession($request, $session);

        if (in_array($session->status, ['assembling', 'completed'], true)) {
            return response()->json(['status' => $session->status]);
        }
        if ($session->status !== 'uploading') {
            return response()->json(['message' => 'Sesi unggah sudah tidak aktif.'], 409);
        }

        $received = $this->received($session);
        if (count($received) !== $session->total_chunks) {
            $missing = array_values(array_diff(range(0, $session->total_chunks - 1), $received));

            return response()->json(['message' => 'Masih ada chunk yang belum diterima.', 'missing' => $missing], 422);
        }

        $session->update(['status' => 'assembling']);
        AssembleUpload::dispatch($session->id);

        return response()->json(['status' => 'assembling']);
    }

    public function cancel(Request $request, UploadSession $session): JsonResponse
    {
        $this->authorizeSession($request, $session);

        if ($session->status === 'uploading') {
            Storage::disk('local')->deleteDirectory($session->chunkDir());
            $session->update(['status' => 'cancelled']);
        }

        return response()->json(['status' => $session->status]);
    }

    /** Nomor chunk yang sudah tersimpan utuh di server (dibaca dari disk = sumber kebenaran). */
    private function received(UploadSession $session): array
    {
        $disk = Storage::disk('local');

        return collect($disk->files($session->chunkDir()))
            ->map(fn ($path) => (int) pathinfo($path, PATHINFO_FILENAME))
            ->filter(fn (int $i) => $i < $session->total_chunks
                && $disk->size($session->chunkDir() . '/' . UploadSession::chunkName($i)) === $session->expectedSize($i))
            ->sort()
            ->values()
            ->all();
    }

    private function authorizeSession(Request $request, UploadSession $session): void
    {
        abort_unless($session->user_id === $request->user()->id, 403);
    }
}
