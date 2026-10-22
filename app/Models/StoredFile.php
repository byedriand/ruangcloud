<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoredFile extends Model
{
    use SoftDeletes;

    protected $table = 'files';

    protected $guarded = [];

    protected $casts = ['size' => 'integer'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    public function replicas(): HasMany
    {
        return $this->hasMany(FileReplica::class, 'file_id');
    }

    public function shares(): HasMany
    {
        return $this->hasMany(ShareLink::class, 'file_id');
    }
}
