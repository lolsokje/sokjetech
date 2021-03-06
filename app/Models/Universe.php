<?php

namespace App\Models;

use App\Enums\UniverseVisibility;
use App\Traits\Snowflake;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Universe extends Model
{
    use HasFactory, Snowflake;

    protected $casts = [
        'visibility' => UniverseVisibility::class,
    ];

    public function scopeVisible(Builder $query): Builder
    {
        if (auth()->check()) {
            $query->where(function (Builder $query) {
                $query->where('visibility', UniverseVisibility::AUTH)
                    ->orWhere('user_id', auth()->user()->id);
            });
        }
        return $query->orWhere('visibility', UniverseVisibility::PUBLIC);
    }

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
}
