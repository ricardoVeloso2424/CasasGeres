<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class House extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'description',
        'location',
        'address_optional',
        'latitude',
        'longitude',
        'is_active',
        'featured',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_active' => 'boolean',
        'featured' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function rentalUnits(): HasMany
    {
        return $this->hasMany(RentalUnit::class);
    }

    public function units(): HasMany
    {
        return $this->rentalUnits();
    }

    public function photos(): MorphMany
    {
        return $this->morphMany(Photo::class, 'imageable')->orderBy('sort_order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function coverImage(): ?Photo
    {
        return $this->photos->firstWhere('is_cover', true) ?? $this->photos->first();
    }

    public function coverImageUrl(): ?string
    {
        return $this->coverImage()?->url;
    }
}
