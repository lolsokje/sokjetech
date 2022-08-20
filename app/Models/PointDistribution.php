<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointDistribution extends SnowflakeModel
{
    use HasFactory;

    protected $hidden = [
        'id',
        'point_system_id',
        'created_at',
        'modified_at',
    ];

    public function pointSystem(): BelongsTo
    {
        return $this->belongsTo(PointSystem::class);
    }
}
