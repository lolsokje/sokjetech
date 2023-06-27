<?php

namespace App\Models;

use App\Builders\RaceBuilder;
use App\Contracts\RaceDuration;
use App\Enums\DistanceType;
use App\Enums\RaceType;
use App\Support\RaceDuration\Distance;
use App\Support\RaceDuration\Lap;
use App\Support\RaceDuration\Time;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Race extends SnowflakeModel
{
    use HasFactory;

    protected $casts = [
        'order' => 'integer',
        'race_type' => RaceType::class,
        'distance_type' => DistanceType::class,
        'qualifying_started' => 'boolean',
        'qualifying_completed' => 'boolean',
        'started' => 'boolean',
        'completed' => 'boolean',
        'completed_at' => 'datetime',
        'qualifying_details' => 'json',
        'race_details' => 'json',
    ];

    public function universe(): Universe
    {
        return $this->season->universe;
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function circuit(): BelongsTo
    {
        return $this->belongsTo(Circuit::class);
    }

    public function variation(): BelongsTo
    {
        return $this->belongsTo(CircuitVariation::class, 'circuit_variation_id');
    }

    public function qualifyingResults(): HasMany
    {
        return $this->hasMany(QualifyingResult::class);
    }

    public function raceResults(): HasMany
    {
        return $this->hasMany(RaceResult::class);
    }

    public function raceDuration(): RaceDuration
    {
        return match ($this->race_type) {
            RaceType::LAP => new Lap($this),
            RaceType::TIME => new Time($this),
            RaceType::DISTANCE => new Distance($this),
        };
    }

    public static function query(): RaceBuilder
    {
        return parent::query();
    }

    public function newEloquentBuilder($query): RaceBuilder
    {
        return new RaceBuilder($query);
    }
}
