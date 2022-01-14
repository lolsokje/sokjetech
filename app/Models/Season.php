<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Season extends Model
{
    use HasFactory, Uuids;

    protected $casts = [
        'year' => 'integer',
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
        return Attribute::get(fn() => "$this->year {$this->series->name}");
    }

    public function universe(): Attribute
    {
        return Attribute::get(fn() => $this->series->universe);
    }

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }

    public function races(): HasMany
    {
        return $this->hasMany(Race::class);
    }

    public function teams(): HasMany
    {
        return $this->series->universe->teams()->orderBy('full_name');
    }

    public function engines(): HasMany
    {
        return $this->series->engines()->orderBy('name');
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
        return $this->drivers()->get()->map(fn(Racer $driver) => $driver->number)->toArray();
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
}
