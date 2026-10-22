<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StorageNode extends Model
{
    protected $guarded = [];

    protected $casts = [
        'capacity_bytes'  => 'integer',
        'last_checked_at' => 'datetime',
    ];

    public function replicas(): HasMany
    {
        return $this->hasMany(FileReplica::class);
    }

    public function usedBytes(): int
    {
        return (int) FileReplica::query()
            ->where('file_replicas.storage_node_id', $this->id)
            ->where('file_replicas.status', 'synced')
            ->join('files', 'files.id', '=', 'file_replicas.file_id')
            ->sum('files.size');
    }

    public function freeBytes(): int
    {
        return max(0, $this->capacity_bytes - $this->usedBytes());
    }

    public function usagePercent(): int
    {
        if ($this->capacity_bytes <= 0) {
            return 0;
        }

        return (int) min(100, round($this->usedBytes() / $this->capacity_bytes * 100));
    }
}
