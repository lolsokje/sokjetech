<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Circuit extends SnowflakeModel
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function races(): HasMany
    {
        return $this->hasMany(Race::class);
    }

    public function scopeSearch(Builder $query, ?string $search = ''): Builder
    {
        return $query->where('name', 'LIKE', '%' . $search . '%')
            ->orWhere('country', 'LIKE', '%' . $search . '%');
    }

    public function scopeSort(Builder $query, ?string $field, ?string $direction): Builder
    {
        $field = $field ?? 'name';
        $direction = $direction ?? 'asc';
        return $query->orderBy($field, $direction);
    }
}
