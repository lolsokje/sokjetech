<?php

namespace App\Models;

use App\Traits\Snowflake;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Engine extends Model
{
    use HasFactory, Snowflake;

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }

    public function universe(): HasManyThrough
    {
        return $this->hasManyThrough(Universe::class, Series::class);
    }
}
