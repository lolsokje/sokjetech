<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Season extends Model
{
    use HasFactory, Uuids;

    protected $casts = [
        'year' => 'integer',
    ];

    protected $appends = [
        'fullName',
        'universe',
    ];

    public function getFullNameAttribute(): string
    {
        return "$this->year {$this->series->name} season";
    }

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }

    public function getUniverseAttribute(): Universe
    {
        return $this->series->universe;
    }

    public function races(): HasMany
    {
        return $this->hasMany(Race::class);
    }
}
