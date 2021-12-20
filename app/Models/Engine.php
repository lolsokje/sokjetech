<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Engine extends Model
{
    use HasFactory, Uuids;

    protected $guarded = [];

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }

    public function universe(): HasManyThrough
    {
        return $this->hasManyThrough(Universe::class, Series::class);
    }
}
