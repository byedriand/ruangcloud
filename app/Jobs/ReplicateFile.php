<?php

namespace App\Jobs;

use App\Models\ActivityLog;
use App\Models\StoredFile;
use App\Services\StorageNodeService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;
use Throwable;

/** Menyalin file dari replika yang sehat ke node lain secara asinkron (KAK F-05). */
class ReplicateFile implements ShouldQueue
{
    use Queueable;

    public int $timeout = 3600;
    public int $tries = 1;

    public function __construct(public int $fileId)
    {
    }

    public function handle(StorageNodeService $nodes): void
    {
        $file = StoredFile::with('replicas.node')->find($this->fileId);
        if (! $file) {
            return;
        }

        $source = $file->replicas->first(
            fn ($r) => $r->status === 'synced' && $r->node->status === 'active'
        );
        if (! $source) {
            return;
        }

        $srcDisk = $nodes->disk($source->node);
        $copied = 0;

        foreach ($file->replicas->where('status', '!=', 'synced') as $replica) {
            if ($replica->node->status !== 'active') {
                continue;
            }

            try {
                $dstDisk = $nodes->disk($replica->node);
                $stream = $srcDisk->readStream($file->stored_path);
                $dstDisk->writeStream($file->stored_path, $stream);
                if (is_resource($stream)) {
                    fclose($stream);
                }

                // Verifikasi integritas replika (SHA-256).
                if (hash_file('sha256', $dstDisk->path($file->stored_path)) !== $file->checksum_sha256) {
                    $dstDisk->delete($file->stored_path);
                    throw new \RuntimeException('Checksum replika tidak cocok.');
                }

                $replica->update(['status' => 'synced', 'last_error' => null]);
                $copied++;
            } catch (Throwable $e) {
                report($e);
                $replica->update(['status' => 'failed', 'last_error' => Str::limit($e->getMessage(), 250)]);
            }
        }

        if ($copied > 0) {
            ActivityLog::record(
                'replicate',
                'Replikasi "' . $file->name . '" selesai (' . $copied . ' node)',
                ['file_id' => $file->id],
                $file->user_id,
            );
        }
    }
}
