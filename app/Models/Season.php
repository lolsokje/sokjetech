<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;

class Season extends SnowflakeModel
{
    use HasFactory;

    protected $casts = [
        'year' => 'integer',
        'started' => 'boolean',
        'completed' => 'boolean',
    ];

    protected $appends = [
        'full_name',
    ];

    public function fullName(): Attribute
    {
        return Attribute::get(fn () => "$this->year $this->name");
    }

    public function universe(): Attribute
    {
        return Attribute::get(fn () => $this->series->universe);
    }

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }

    public function races(): HasMany
    {
        return $this->hasMany(Race::class);
    }

    public function nextRace(): ?Race
    {
        return Race::query()
            ->where('completed', false)
            ->where('season_id', $this->id)
            ->orderBy('order')
            ->first();
    }

    public function teams(): HasMany
    {
        return $this->series->universe->teams()->orderBy('full_name');
    }

    public function baseEngines(): HasMany
    {
        return $this->series->engines()->orderBy('name');
    }

    public function engines(): HasMany
    {
        return $this->hasMany(EngineSeason::class);
    }

    public function entrants(): HasMany
    {
        return $this->hasMany(Entrant::class);
    }

    public function drivers(): HasMany
    {
        return $this->hasMany(Racer::class);
    }

    public function activeRacers(): HasMany
    {
        return $this->drivers()->where('active', true);
    }

    public function pickedNumbers(): array
    {
        return $this->drivers()->get()->map(fn (Racer $driver) => $driver->number)->toArray();
    }

    public function availableDrivers(): Collection
    {
        return $this->universe->drivers()
            ->whereNotIn('id', $this->drivers()
                ->where('active', true)
                ->pluck('driver_id'))
            ->orderBy('first_name')
            ->get();
    }

    public function qualifyingFormat(): MorphTo
    {
        return $this->morphTo('format');
    }

    public function pointSystem(): HasOne
    {
        return $this->hasOne(PointSystem::class);
    }

    public function pointDistribution(): HasManyThrough
    {
        return $this->hasManyThrough(PointDistribution::class, PointSystem::class);
    }

    public function points(): array
    {
        $points = $this->pointDistribution()->get();

        return $points->map(fn (PointDistribution $point) => [
            'position' => $point->position,
            'points' => $point->points,
        ])->toArray();
    }

    public function reliabilityConfiguration(): HasOne
    {
        return $this->hasOne(ReliabilityConfiguration::class);
    }

    public function reliabilityReasons(): HasMany
    {
        return $this->hasMany(ReliabilityReason::class);
    }

    public function qualifyingResults(): HasMany
    {
        return $this->hasMany(QualifyingResult::class);
    }

    public function raceResults(): HasMany
    {
        return $this->hasMany(RaceResult::class);
    }

    public function canStart(): Attribute
    {
        return Attribute::get(function () {
            return !$this->started &&
                $this->qualifyingFormat !== null &&
                $this->pointSystem !== null &&
                $this->reliabilityConfiguration !== null &&
                count($this->races) !== 0;
        });
    }

    public function canComplete(): Attribute
    {
        return Attribute::get(fn () => !$this->completed && $this->started && !$this->nextRace());
    }

    public function hasActiveRace(): Attribute
    {
        return Attribute::get(function () {
            return $this->races()->where('qualifying_started', true)->where('completed', false)->count() > 0;
        });
    }

    public function poles(): HasMany
    {
        return $this->hasMany(QualifyingResult::class)->with([
            'racer' => [
                'driver',
                'entrant',
            ],
        ])->where('position', 1);
    }

    public function winners(): HasMany
    {
        return $this->hasMany(RaceResult::class)->with([
            'racer' => [
                'driver',
                'entrant',
            ],
        ])->where('position', 1);
    }
}
