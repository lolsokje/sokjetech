<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Engine extends SnowflakeModel
{
    use HasFactory;

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }

    public function universe(): HasManyThrough
    {
        return $this->hasManyThrough(Universe::class, Series::class);
    }
}
