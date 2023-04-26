<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverChampionshipStanding extends SnowflakeModel
{
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
