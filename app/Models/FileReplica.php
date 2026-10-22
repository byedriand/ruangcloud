<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FileReplica extends Model
{
    protected $guarded = [];

    protected $casts = ['is_primary' => 'boolean'];

    public function node(): BelongsTo
    {
        return $this->belongsTo(StorageNode::class, 'storage_node_id');
    }

    public function file(): BelongsTo
    {
        return $this->belongsTo(StoredFile::class, 'file_id');
    }
}
