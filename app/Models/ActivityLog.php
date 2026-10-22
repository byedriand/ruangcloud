<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    protected $guarded = [];

    protected $casts = ['meta' => 'array'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Catat aktivitas penting untuk audit trail (KAK F-09). */
    public static function record(string $action, string $description, array $meta = [], ?int $userId = null): self
    {
        return static::create([
            'user_id'     => $userId ?? Auth::id(),
            'action'      => $action,
            'description' => $description,
            'meta'        => $meta ?: null,
        ]);
    }
}
