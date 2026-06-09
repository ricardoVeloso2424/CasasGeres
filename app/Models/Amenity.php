<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Amenity extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function rentalUnits(): BelongsToMany
    {
        return $this->belongsToMany(RentalUnit::class)->withTimestamps();
    }
}
