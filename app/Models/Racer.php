<?php

namespace App\Models;

use App\Contracts\Development\HasRatingHistory;
use App\Contracts\Development\IsDevelopmentEntity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Racer extends SnowflakeModel implements HasRatingHistory, IsDevelopmentEntity
{
    use HasFactory;

    protected $casts = [
        'number' => 'integer',
        'active' => 'boolean',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function entrant(): BelongsTo
    {
        return $this->belongsTo(Entrant::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function qualifyingResult(): HasMany
    {
        return $this->hasMany(QualifyingResult::class);
    }

    public function raceResults(): HasMany
    {
        return $this->hasMany(RaceResult::class);
    }

    public function age(): int
    {
        return $this->driver->age($this->season);
    }

    public function getComponentRating(string $component): ?int
    {
        return $this->getAttribute($component);
    }

    public function getHistoryTableQuery(): Builder
    {
        return DriverDevelopmentHistory::query();
    }

    public function getLabel(): string
    {
        return $this->driver->full_name;
    }

    public function getStyleString(): string
    {
        return $this->entrant->style_string;
    }

    public function getExtra(): array
    {
        return [
            'accent' => $this->entrant->accent_colour,
            'number' => $this->number,
            'team' => $this->entrant->full_name,
            'age' => $this->age(),
        ];
    }
}
