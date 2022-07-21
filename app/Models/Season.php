<?php

namespace App\Models;

use App\Traits\Snowflake;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Collection;

class Season extends Model
{
    use HasFactory, Snowflake;

    protected $casts = [
        'year' => 'integer',
        'started' => 'boolean',
        'completed' => 'boolean',
    ];

    protected $appends = [
        'full_name',
        'universe',
    ];

    protected $with = [
        'series',
    ];

    public function fullName(): Attribute
    {
        return Attribute::get(fn () => "$this->year {$this->series->name}");
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

    public function qualifyingResults(): HasMany
    {
        return $this->hasMany(QualifyingResult::class);
    }

    public function raceResults(): HasMany
    {
        return $this->hasMany(RaceResult::class);
    }
}
