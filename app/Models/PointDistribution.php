<?php

namespace App\Models;

use App\Traits\Snowflake;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PointDistribution extends Model
{
    use HasFactory, Snowflake;

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
