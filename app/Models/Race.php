<?php

namespace App\Models;

use App\Builders\RaceBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Race extends SnowflakeModel
{
    use HasFactory;

    protected $casts = [
        'order' => 'integer',
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

    public function stints(): HasMany
    {
        return $this->hasMany(Stint::class);
    }

    public function qualifyingResults(): HasMany
    {
        return $this->hasMany(QualifyingResult::class);
    }

    public function raceResults(): HasMany
    {
        return $this->hasMany(RaceResult::class);
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
