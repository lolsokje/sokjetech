<?php

namespace App\Models;

use App\Contracts\Development\HasRatingHistory;
use App\Contracts\Development\IsDevelopmentEntity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EngineSeason extends SnowflakeModel implements HasRatingHistory, IsDevelopmentEntity
{
    use HasFactory;

    protected $casts = [
        'rebadge' => 'bool',
        'individual_rating' => 'bool',
    ];

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function baseEngine(): BelongsTo
    {
        return $this->belongsTo(Engine::class, 'base_engine_id');
    }

    public function developmentHistories(): HasMany
    {
        return $this->hasMany(EngineDevelopmentHistory::class);
    }

    public function getComponentRating(string $component): ?int
    {
        return $this->getAttribute($component);
    }

    /**
     * @return Builder<EngineSeason>
     */
    public function getHistoryTableQuery(): Builder
    {
        return EngineDevelopmentHistory::query();
    }

    public function getLabel(): string
    {
        return $this->name;
    }

    public function getStyleString(): string
    {
        return '';
    }

    public function getExtra(): array
    {
        return [
            'rebadged' => $this->rebadge,
            'individual_rating' => $this->individual_rating,
            'base_engine_id' => $this->base_engine_id,
        ];
    }
}
