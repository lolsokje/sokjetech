<?php

namespace App\Models;

use App\Builders\StintBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stint extends SnowflakeModel
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'reliability' => 'bool',
        'use_team_rating' => 'bool',
        'use_driver_rating' => 'bool',
        'use_engine_rating' => 'bool',
    ];

    public function race(): BelongsTo
    {
        return $this->belongsTo(Race::class);
    }

    public static function query(): StintBuilder
    {
        return parent::query();
    }

    public function newEloquentBuilder($query): StintBuilder
    {
        return new StintBuilder($query);
    }
}
