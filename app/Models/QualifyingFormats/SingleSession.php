<?php

namespace App\Models\QualifyingFormats;

use App\Models\Season;
use App\Models\SnowflakeModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SingleSession extends SnowflakeModel
{
    use HasFactory;

    public function season(): MorphMany
    {
        return $this->morphMany(Season::class, 'format');
    }
}
