<?php

namespace App\Models;

use App\Contracts\Development\HasRatingHistory;
use App\Contracts\Development\IsDevelopmentEntity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Entrant extends SnowflakeModel implements HasRatingHistory, IsDevelopmentEntity
{
    use HasFactory;

    protected $appends = [
        'style_string',
    ];

    public function styleString(): Attribute
    {
        return Attribute::get(function () {
            $primary = $this->primary_colour;
            $secondary = $this->secondary_colour;

            return "background-color:$primary;color:$secondary;font-weight:bold";
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

    public function racersWithParticipation(): HasMany
    {
        return $this->hasMany(Racer::class)->whereHas('raceResults');
    }

    public function qualifyingResults(): HasMany
    {
        return $this->hasMany(QualifyingResult::class);
    }

    public function raceResults(): HasMany
    {
        return $this->hasMany(RaceResult::class);
    }

    public function developmentHistories(): HasMany
    {
        return $this->hasMany(TeamDevelopmentHistory::class);
    }

    public function getComponentRating(string $component): ?int
    {
        // TODO individual car components
        return $this->getAttribute($component);
    }

    /**
     * @return Builder<Entrant>
     */
    public function getHistoryTableQuery(): Builder
    {
        return TeamDevelopmentHistory::query();
    }

    public function getLabel(): string
    {
        return $this->full_name;
    }

    public function getStyleString(): string
    {
        return $this->style_string;
    }

    public function getExtra(): array
    {
        return [
            'accent' => $this->accent_colour,
        ];
    }
}
