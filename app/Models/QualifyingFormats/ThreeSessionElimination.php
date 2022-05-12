<?php

namespace App\Models\QualifyingFormats;

use App\Models\Season;
use App\Traits\Snowflake;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ThreeSessionElimination extends Model
{
    use HasFactory, Snowflake;

    public function season(): MorphMany
    {
        return $this->morphMany(Season::class, 'format');
    }
}
