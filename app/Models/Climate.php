<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Climate extends SnowflakeModel
{
    use HasFactory;

    public function conditions(): HasMany
    {
        return $this->hasMany(ClimateCondition::class);
    }
}
