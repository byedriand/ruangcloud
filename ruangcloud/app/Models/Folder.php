<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Folder extends Model
{
    protected $guarded = [];

    public function files(): HasMany
    {
        return $this->hasMany(StoredFile::class, 'folder_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    /** Id folder ini + semua subfolder di bawahnya (rekursif). */
    public function descendantIds(): array
    {
        $ids = [];
        $queue = [$this->id];

        while ($queue !== []) {
            $children = static::whereIn('parent_id', $queue)->pluck('id')->all();
            $children = array_values(array_diff($children, $ids));
            $ids = array_merge($ids, $children);
            $queue = $children;
        }

        return $ids;
    }

    /** Id folder ini beserta seluruh keturunannya. */
    public function subtreeIds(): array
    {
        return array_merge([$this->id], $this->descendantIds());
    }

    public function totalFiles(): int
    {
        return StoredFile::whereIn('folder_id', $this->subtreeIds())->count();
    }

    public function totalSubfolders(): int
    {
        return count($this->descendantIds());
    }

    public function totalSize(): int
    {
        return (int) StoredFile::whereIn('folder_id', $this->subtreeIds())->sum('size');
    }

    /**
     * Semua file di folder ini dan subfoldernya, masing-masing disertai
     * path relatif bergaya "Folder/Sub/File.ext" (untuk ZIP & halaman publik).
     *
     * @return array<int, array{0: StoredFile, 1: string}>
     */
    public function archiveEntries(): array
    {
        $folders = static::whereIn('id', $this->subtreeIds())->get()->keyBy('id');
        $files = StoredFile::whereIn('folder_id', $folders->keys()->all())
            ->orderBy('name')
            ->get();

        $entries = [];
        foreach ($files as $file) {
            $parts = [$file->name];
            $cursor = $folders->get($file->folder_id);
            while ($cursor && $cursor->id !== $this->id) {
                array_unshift($parts, $cursor->name);
                $cursor = $folders->get($cursor->parent_id);
            }
            array_unshift($parts, $this->name);
            $entries[] = [$file, implode('/', $parts)];
        }

        return $entries;
    }
}
