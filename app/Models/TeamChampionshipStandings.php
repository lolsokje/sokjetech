<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamChampionshipStandings extends SnowflakeModel
{
    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function entrant(): BelongsTo
    {
        return $this->belongsTo(Entrant::class);
    }
}
