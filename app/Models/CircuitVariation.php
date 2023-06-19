<?php

namespace App\Models;

use App\Support\LaptimeFormatter;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CircuitVariation extends SnowflakeModel
{
    use HasFactory;

    protected $appends = [
        'length_in_kilometers',
        'length_in_miles',
        'readable_laptime',
    ];

    public function lengthInKilometers(): Attribute
    {
        return Attribute::get(fn (): float => (float) $this->length / 1000);
    }

    public function lengthInMiles(): Attribute
    {
        return Attribute::get(fn () => round($this->length * 0.000621, 3));
    }

    public function baseLapTime(): Attribute
    {
        return Attribute::set(fn (string $laptime) => LaptimeFormatter::toInteger($laptime));
    }

    public function readableLaptime(): Attribute
    {
        return Attribute::get(fn () => LaptimeFormatter::toString($this->base_laptime));
    }

    public function circuit(): BelongsTo
    {
        return $this->belongsTo(Circuit::class);
    }
}
