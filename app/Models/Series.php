<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Series extends Model
{
    use HasFactory, Uuids;

    public function universe(): BelongsTo
    {
        return $this->belongsTo(Universe::class);
    }

    public function engines(): HasMany
    {
        return $this->hasMany(Engine::class);
    }

    public function user(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, Universe::class);
    }

    public function seasons(): HasMany
    {
        return $this->hasMany(Season::class);
    }
}
