<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Photo extends Model
{
    protected $fillable = [
        'path',
        'alt',
        'sort_order',
        'is_cover',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_cover' => 'boolean',
    ];

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getUrlAttribute(): string
    {
        if (Str::startsWith($this->path, ['http://', 'https://', '//'])) {
            return $this->path;
        }

        return Storage::disk('public')->url($this->path);
    }

    public function isStoredLocally(): bool
    {
        return ! Str::startsWith($this->path, ['http://', 'https://', '//']);
    }
}
