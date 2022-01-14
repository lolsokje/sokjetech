<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Driver extends Model
{
    use HasFactory, Uuids;

    protected $appends = [
        'full_name',
        'readable_dob',
        'edit_dob',
    ];

    protected $casts = [
        'dob' => 'datetime',
    ];

    public function readableDob(): Attribute
    {
        return Attribute::get(fn() => $this->dob->format('F jS, Y'));
    }


    public function editDob(): Attribute
    {
        return Attribute::get(fn() => $this->dob->format('Y-m-d'));
    }

    public function fullName(): Attribute
    {
        return Attribute::get(fn() => trim("$this->first_name $this->last_name"));
    }

    public function universe(): BelongsTo
    {
        return $this->belongsTo(Universe::class);
    }

    public function user(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, Universe::class);
    }

    public function racers(): HasMany
    {
        return $this->hasMany(Racer::class);
    }
}
