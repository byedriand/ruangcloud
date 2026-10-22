<?php

namespace App\Console\Commands;

use App\Jobs\ReplicateFile;
use App\Models\FileReplica;
use App\Models\StorageNode;
use App\Models\StoredFile;
use App\Services\StorageNodeService;
use Illuminate\Console\Command;

class NodesCheck extends Command
{
    protected $signature = 'nodes:check';
    protected $description = 'Health check storage node, lengkapi replika yang kurang, dan ulangi replikasi yang gagal';

    public function handle(StorageNodeService $nodes): int
    {
        foreach (StorageNode::all() as $node) {
            $ok = $nodes->check($node);
            $this->line(sprintf('%-12s %s', $node->name, $ok ? 'aktif' : 'DOWN'));
        }

        $want = (int) config('nodes.replicas');

        // Tambah replika bila jumlahnya kurang (mis. node sempat mati saat file diunggah).
        StoredFile::with('replicas')->chunkById(100, function ($files) use ($nodes, $want) {
            foreach ($files as $file) {
                $missing = $want - $file->replicas->count();
                if ($missing <= 0) {
                    continue;
                }
                $extra = $nodes->pick($file->size, $missing, $file->replicas->pluck('storage_node_id')->all());
                foreach ($extra as $node) {
                    FileReplica::create(['file_id' => $file->id, 'storage_node_id' => $node->id, 'status' => 'pending']);
                }
            }
        });

        // Jadwalkan ulang replikasi yang pending/failed.
        $ids = StoredFile::whereHas('replicas', fn ($q) => $q->whereIn('status', ['pending', 'failed']))->pluck('id');
        foreach ($ids as $id) {
            ReplicateFile::dispatch($id);
        }
        $this->info($ids->count() . ' file dijadwalkan untuk replikasi ulang.');

        return self::SUCCESS;
    }
}
