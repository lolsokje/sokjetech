<?php

namespace App\Models\QualifyingFormats;

use App\Contracts\HasSessions;
use App\Models\Season;
use App\Models\SnowflakeModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ThreeSessionElimination extends SnowflakeModel implements HasSessions
{
    use HasFactory;

    public function season(): MorphMany
    {
        return $this->morphMany(Season::class, 'format');
    }

    public function sessions(): int
    {
        return 3;
    }
}
