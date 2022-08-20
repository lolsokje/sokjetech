<?php

namespace App\Models;

use App\Builders\UniverseBuilder;
use App\Enums\UniverseVisibility;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Universe extends SnowflakeModel
{
    use HasFactory;

    protected $casts = [
        'visibility' => UniverseVisibility::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function drivers(): HasMany
    {
        return $this->hasMany(Driver::class);
    }

    public function series(): HasMany
    {
        return $this->hasMany(Series::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }

    public function engines(): HasManyThrough
    {
        return $this->hasManyThrough(Engine::class, Series::class);
    }

    public static function query(): UniverseBuilder
    {
        return parent::query();
    }

    public function newEloquentBuilder($query): UniverseBuilder
    {
        return new UniverseBuilder($query);
    }
}
