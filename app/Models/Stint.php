<?php

namespace App\Models;

use App\Traits\Snowflake;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stint extends Model
{
    use HasFactory, Snowflake;

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
}
