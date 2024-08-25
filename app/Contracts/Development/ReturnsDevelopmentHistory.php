<?php

declare(strict_types=1);

namespace App\Contracts\Development;

use App\Models\Race;
use App\Models\Season;
use App\ValueObjects\Season\Development\DevelopmentHistoryEntity;
use Illuminate\Support\Collection;

interface ReturnsDevelopmentHistory
{
    /**
     * @param Collection<Race> $races
     *
     * @return array<DevelopmentHistoryEntity>
     */
    public function handle(
        Season $season,
        Collection $races,
        string $component,
    ): array;
}
