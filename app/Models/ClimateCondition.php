<?php

namespace App\Models;

use App\Enums\WeatherConditions;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClimateCondition extends SnowflakeModel
{
    use HasFactory;

    protected $casts = [
        'condition' => WeatherConditions::class,
    ];

    protected $appends = [
        'condition_name',
    ];

    public function conditionName(): Attribute
    {
        return Attribute::get(fn () => $this->condition->label());
    }

    public function climate(): BelongsTo
    {
        return $this->belongsTo(Climate::class);
    }
}
