<?php

declare(strict_types=1);

namespace App\Actions\Season\Development;

use App\Models\Racer;
use App\Models\Season;
use App\ValueObjects\Season\Development\DevelopmentEntity;

final readonly class GetDevelopmentDrivers implements ReturnsDevelopmentEntities
{
    /**
     * @return array<DevelopmentEntity>
     */
    public function handle(
        Season $season,
        string $attribute,
    ): array {
        $drivers = $season
            ->activeRacers()
            ->with('season', 'driver', 'entrant')
            ->orderBy('number')
            ->get();

        return $drivers->map(fn(Racer $racer) => DevelopmentEntity::fromModel($racer, $attribute))->toArray();
    }
}
