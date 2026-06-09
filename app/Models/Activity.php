<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Activity extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'category',
        'description',
        'location',
        'distance',
        'image',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function photos(): MorphMany
    {
        return $this->morphMany(Photo::class, 'imageable')->orderBy('sort_order');
    }

    public function coverImage(): ?Photo
    {
        return $this->photos->firstWhere('is_cover', true) ?? $this->photos->first();
    }

    public function coverImageUrl(): ?string
    {
        return $this->coverImage()?->url ?? $this->image;
    }
}
