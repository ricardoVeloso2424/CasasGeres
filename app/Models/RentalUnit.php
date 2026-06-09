<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;

class RentalUnit extends Model
{
    protected $fillable = [
        'house_id',
        'name',
        'slug',
        'type',
        'short_description',
        'description',
        'capacity',
        'bedrooms',
        'bathrooms',
        'base_price',
        'rules',
        'is_active',
        'featured',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'base_price' => 'decimal:2',
        'is_active' => 'boolean',
        'featured' => 'boolean',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function house(): BelongsTo
    {
        return $this->belongsTo(House::class);
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class)->withTimestamps();
    }

    public function calendarSources(): HasMany
    {
        return $this->hasMany(CalendarSource::class);
    }

    public function blockedDates(): HasMany
    {
        return $this->hasMany(BlockedDate::class);
    }

    public function bookingRequests(): HasMany
    {
        return $this->hasMany(BookingRequest::class);
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

    public function hasBlockedDatesBetween(string|DateTimeInterface $checkIn, string|DateTimeInterface $checkOut): bool
    {
        $checkIn = Carbon::parse($checkIn)->startOfDay();
        $checkOut = Carbon::parse($checkOut)->startOfDay();

        return $this->blockedDates()
            ->where('starts_at', '<', $checkOut)
            ->where('ends_at', '>', $checkIn)
            ->exists();
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
