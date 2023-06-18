<?php

namespace App\Models;

use App\Support\LaptimeFormatter;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CircuitVariation extends Model
{
    use HasFactory;

    protected $appends = [
        'readable_laptime',
    ];

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
