<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EngineSeason extends SnowflakeModel
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
}
