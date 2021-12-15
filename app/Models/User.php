<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, Uuids;

    protected $guarded = [];

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
}
