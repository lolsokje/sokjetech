<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PointSystem extends SnowflakeModel
{
    use HasFactory;

    protected $casts = [
        'fastest_lap_point_awarded' => 'boolean',
        'pole_position_point_awarded' => 'boolean',
    ];

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function pointDistributions(): HasMany
    {
        return $this->hasMany(PointDistribution::class);
    }
}
