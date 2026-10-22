<?php

namespace App\Services;

use App\Models\StorageNode;
use App\Models\StoredFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Akses ke storage node. Saat ini tiap node = satu folder lokal (driver "local").
 * Untuk node di server lain / MinIO, cukup ganti isi disk() menjadi driver "s3"
 * atau path jaringan; kode lain tidak perlu berubah.
 */
class StorageNodeService
{
    public function disk(StorageNode $node)
    {
        return Storage::build([
            'driver' => 'local',
            'root'   => $node->path,
            'throw'  => true,
        ]);
    }

    /** Pilih node aktif dengan ruang kosong terbesar (KAK F-05). */
    public function pick(int $size, int $copies, array $excludeIds = []): Collection
    {
        return StorageNode::query()
            ->where('status', 'active')
            ->whereNotIn('id', $excludeIds)
            ->get()
            ->filter(fn (StorageNode $n) => $n->freeBytes() >= $size)
            ->sortByDesc(fn (StorageNode $n) => $n->freeBytes())
            ->take($copies)
            ->values();
    }

    /** Health check: tulis lalu baca file kecil di node. */
    public function check(StorageNode $node): bool
    {
        try {
            $disk = $this->disk($node);
            $token = (string) microtime(true);
            $disk->put('.health', $token);
            $ok = $disk->get('.health') === $token;
        } catch (Throwable) {
            $ok = false;
        }

        $node->update(['status' => $ok ? 'active' : 'down', 'last_checked_at' => now()]);

        return $ok;
    }

    /**
     * Respons unduhan dari replika sehat pertama; bila node mati / file hilang pindah ke replika lain
     * (failover). Mengembalikan null bila semua replika tidak dapat diakses.
     */
    public function downloadResponse(StoredFile $file)
    {
        $replicas = $file->replicas()->with('node')
            ->where('status', 'synced')
            ->orderByDesc('is_primary')
            ->get();

        foreach ($replicas as $replica) {
            if ($replica->node->status !== 'active') {
                continue;
            }

            $disk = $this->disk($replica->node);
            if (! $disk->exists($file->stored_path)) {
                $replica->update(['status' => 'failed', 'last_error' => 'File tidak ditemukan di node.']);
                continue;
            }

            return $disk->download($file->stored_path, $file->name, [
                'Content-Type' => $file->mime ?: 'application/octet-stream',
            ]);
        }

        return null;
    }
}
