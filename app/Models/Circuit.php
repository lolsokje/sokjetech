<?php

namespace App\Models;

use App\Builders\CircuitBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Circuit extends SnowflakeModel
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function races(): HasMany
    {
        return $this->hasMany(Race::class);
    }

    public static function query(): CircuitBuilder
    {
        return parent::query();
    }

    public function newEloquentBuilder($query): CircuitBuilder
    {
        return new CircuitBuilder($query);
    }
}
