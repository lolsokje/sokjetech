<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\Development\HasDevelopmentHistory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class EngineDevelopmentHistory extends Model implements HasDevelopmentHistory
{
    use HasFactory;

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function engineSeason(): BelongsTo
    {
        return $this->belongsTo(EngineSeason::class);
    }

    public function race(): BelongsTo
    {
        return $this->belongsTo(Race::class);
    }

    public function getCurrentDevelopment(): ?int
    {
        return $this->development;
    }
}
