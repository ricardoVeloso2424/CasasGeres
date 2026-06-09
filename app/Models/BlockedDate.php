<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlockedDate extends Model
{
    protected $fillable = [
        'rental_unit_id',
        'calendar_source_id',
        'source',
        'external_uid',
        'starts_at',
        'ends_at',
        'summary',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at' => 'date',
    ];

    public function rentalUnit(): BelongsTo
    {
        return $this->belongsTo(RentalUnit::class);
    }

    public function calendarSource(): BelongsTo
    {
        return $this->belongsTo(CalendarSource::class);
    }
}
