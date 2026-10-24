<?php

namespace App\Jobs;

use App\Models\ActivityLog;
use App\Models\FileReplica;
use App\Models\StoredFile;
use App\Models\UploadSession;
use App\Models\User;
use App\Services\StorageNodeService;
use App\Support\UniqueName;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

/** Menggabungkan chunk secara sekuensial, hitung SHA-256, simpan ke node utama (KAK 5.2). */
class AssembleUpload implements ShouldQueue
{
    use Queueable;

    public int $timeout = 3600;
    public int $tries = 1;

    public function __construct(public string $sessionId)
    {
    }

    public function handle(StorageNodeService $nodes): void
    {
        $session = UploadSession::find($this->sessionId);
        if (! $session || $session->status !== 'assembling') {
            return;
        }

        $local = Storage::disk('local');
        $tmp = $local->path("tmp/{$session->id}.bin");

        try {
            $paths = [];
            for ($i = 0; $i < $session->total_chunks; $i++) {
                $rel = $session->chunkDir() . '/' . UploadSession::chunkName($i);
                if (! $local->exists($rel) || $local->size($rel) !== $session->expectedSize($i)) {
                    throw new RuntimeException("Chunk {$i} hilang atau ukurannya tidak sesuai.");
                }
                $paths[] = $local->path($rel);
            }

            $targets = $nodes->pick($session->file_size, (int) config('nodes.replicas'));
            if ($targets->isEmpty()) {
                throw new RuntimeException('Tidak ada storage node aktif dengan kapasitas cukup.');
            }
            $primary = $targets->first();

            // Gabungkan chunk berurutan ke file sementara.
            if (! is_dir(dirname($tmp))) {
                mkdir(dirname($tmp), 0775, true);
            }
            $out = fopen($tmp, 'wb');
            foreach ($paths as $path) {
                $in = fopen($path, 'rb');
                stream_copy_to_stream($in, $out);
                fclose($in);
            }
            fclose($out);

            clearstatcache(true, $tmp);
            if (filesize($tmp) !== $session->file_size) {
                throw new RuntimeException('Ukuran file hasil penggabungan tidak sesuai.');
            }
            $checksum = hash_file('sha256', $tmp);

            // Simpan ke node utama dengan nama acak (tanpa ekstensi dari pengguna).
            $storedPath = $session->user_id . '/' . now()->format('Y/m') . '/' . Str::uuid();
            $stream = fopen($tmp, 'rb');
            $nodes->disk($primary)->writeStream($storedPath, $stream);
            if (is_resource($stream)) {
                fclose($stream);
            }

            $file = DB::transaction(function () use ($session, $targets, $primary, $checksum, $storedPath) {
                // Nama sama di folder yang sama -> beri penanda " (1)", " (2)", dst.
                $uniqueName = UniqueName::resolve(
                    $session->file_name,
                    fn (string $candidate) => StoredFile::withoutTrashed()
                        ->where('user_id', $session->user_id)
                        ->where('folder_id', $session->folder_id)
                        ->where('name', $candidate)
                        ->exists()
                );

                $file = StoredFile::create([
                    'user_id'         => $session->user_id,
                    'folder_id'       => $session->folder_id,
                    'name'            => $uniqueName,
                    'mime'            => $session->mime,
                    'size'            => $session->file_size,
                    'checksum_sha256' => $checksum,
                    'stored_path'     => $storedPath,
                    'status'          => 'ready',
                ]);

                foreach ($targets as $node) {
                    FileReplica::create([
                        'file_id'         => $file->id,
                        'storage_node_id' => $node->id,
                        'is_primary'      => $node->id === $primary->id,
                        'status'          => $node->id === $primary->id ? 'synced' : 'pending',
                    ]);
                }

                $session->update(['status' => 'completed', 'file_id' => $file->id, 'error' => null]);

                return $file;
            });

            $local->deleteDirectory($session->chunkDir());
            @unlink($tmp);

            $user = User::find($session->user_id);
            ActivityLog::record(
                'upload',
                ($user ? Str::before($user->name, ' ') : 'Pengguna') . ' mengunggah "' . $file->name . '"',
                ['file_id' => $file->id, 'size' => $file->size],
                $session->user_id,
            );

            if ($targets->count() > 1) {
                ReplicateFile::dispatch($file->id);
            }
        } catch (Throwable $e) {
            report($e);
            @unlink($tmp);
            $session->update(['status' => 'failed', 'error' => Str::limit($e->getMessage(), 500)]);
        }
    }
}
