<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReliabilityConfiguration extends SnowflakeModel
{
    use HasFactory;

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
    ];

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }
}
