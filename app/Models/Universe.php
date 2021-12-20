<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Universe extends Model
{
    use HasFactory, Uuids;

    public const VISIBILITY_PUBLIC = 1;
    public const VISIBILITY_PRIVATE = 2;
    public const VISIBILITY_AUTH = 3;

    public static function visibilities(): array
    {
        return array_keys(self::visibilityLabels());
    }

    public static function visibilityLabels(): array
    {
        return [
            self::VISIBILITY_PUBLIC => 'Public',
            self::VISIBILITY_PRIVATE => 'Private',
            self::VISIBILITY_AUTH => 'Logged in only',
        ];
    }

    public function scopeVisible(Builder $query): Builder
    {
        if (auth()->check()) {
            $query->where(function (Builder $query) {
                $query->where('visibility', self::VISIBILITY_AUTH)
                    ->orWhere('user_id', auth()->user()->id);
            });
        }
        return $query->orWhere('visibility', self::VISIBILITY_PUBLIC);
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
