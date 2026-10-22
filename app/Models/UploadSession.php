<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class UploadSession extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected $casts = [
        'file_size'     => 'integer',
        'chunk_size'    => 'integer',
        'total_chunks'  => 'integer',
        'expires_at'    => 'datetime',
        'last_chunk_at' => 'datetime',
    ];

    /** Ukuran byte yang seharusnya dimiliki chunk ke-$index (chunk terakhir bisa lebih kecil). */
    public function expectedSize(int $index): int
    {
        if ($index < $this->total_chunks - 1) {
            return $this->chunk_size;
        }

        return $this->file_size - $this->chunk_size * ($this->total_chunks - 1);
    }

    public function chunkDir(): string
    {
        return 'chunks/' . $this->id;
    }

    public static function chunkName(int $index): string
    {
        return sprintf('%06d.part', $index);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
