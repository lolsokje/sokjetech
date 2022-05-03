<?php

namespace App\Models;

use App\Traits\Snowflake;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Team extends Model
{
    use HasFactory, Snowflake;

    protected $appends = [
        'style_string',
    ];

    public function styleString(): Attribute
    {
        return Attribute::get(function () {
            $primary = $this->primary_colour;
            $secondary = $this->secondary_colour;
            return "background-color:$primary;color:$secondary;text-align:center;font-weight:bold";
        });
    }

    public function universe(): BelongsTo
    {
        return $this->belongsTo(Universe::class);
    }

    public function user(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, Universe::class);
    }

    public function entrants(): HasMany
    {
        return $this->hasMany(Entrant::class);
    }
}
