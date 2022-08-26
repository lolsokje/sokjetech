<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Notifications\Notifiable;

class User extends AuthenticatableSnowflake
{
    use HasFactory, Notifiable;

    protected $casts = [
        'id' => 'integer',
    ];

    protected $hidden = [
        'discord_id',
        'created_at',
        'updated_at',
    ];

    public function circuits(): HasMany
    {
        return $this->hasMany(Circuit::class);
    }

    public function universes(): HasMany
    {
        return $this->hasMany(Universe::class);
    }

    public function drivers(): HasManyThrough
    {
        return $this->hasManyThrough(Driver::class, Universe::class);
    }

    public function series(): HasManyThrough
    {
        return $this->hasManyThrough(Series::class, Universe::class);
    }

    public function teams(): HasManyThrough
    {
        return $this->hasManyThrough(Team::class, Universe::class);
    }

    public function bugs(): HasMany
    {
        return $this->hasMany(Bug::class);
    }

    public function suggestions(): HasMany
    {
        return $this->hasMany(Suggestion::class);
    }
}
