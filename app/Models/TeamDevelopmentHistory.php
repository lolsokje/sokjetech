<?php

declare(strict_types=1);

namespace App\Models;

use App\Contracts\Development\HasDevelopmentHistory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class TeamDevelopmentHistory extends Model implements HasDevelopmentHistory
{
    use HasFactory;

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function entrant(): BelongsTo
    {
        return $this->belongsTo(Entrant::class);
    }

    public function race(): BelongsTo
    {
        return $this->belongsTo(Race::class);
    }

    public function raceId(): string|int
    {
        return $this->race_id;
    }

    public function getInitialRating(): ?int
    {
        return $this->initial;
    }

    public function getCurrentDevelopment(): ?int
    {
        return $this->development;
    }
}
