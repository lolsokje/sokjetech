<?php

namespace App\Models;

use App\Traits\Snowflake;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Entrant extends Model
{
    use HasFactory, Snowflake;

    protected $appends = [
        'style_string',
    ];

    public function styleString(): Attribute
    {
        return Attribute::get(function () {
            return "background-color:$this->primary_colour;color:$this->secondary_colour;text-align:center;font-weight:bold";
        });
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function engine(): BelongsTo
    {
        return $this->belongsTo(EngineSeason::class);
    }

    public function activeRacers(): HasMany
    {
        return $this->hasMany(Racer::class)->where('active', true);
    }

    public function allRacers(): HasMany
    {
        return $this->hasMany(Racer::class);
    }
}
