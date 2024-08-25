<?php

declare(strict_types=1);

namespace App\Actions\Season\Development\History;

use App\Contracts\Development\ReturnsDevelopmentHistory;
use App\Models\Racer;
use App\Models\Season;
use App\Traits\GetDevelopmentHistory;
use App\ValueObjects\Season\Development\DevelopmentHistoryEntity;
use Illuminate\Support\Collection;

final class GetDriverDevelopmentHistory implements ReturnsDevelopmentHistory
{
    use GetDevelopmentHistory;

    /**
     * @return array<DevelopmentHistoryEntity>
     */
    public function handle(
        Season $season,
        Collection $races,
        string $component,
    ): array {
        $this->setup(
            season: $season,
            races: $races,
            component: $component,
        );

        $developmentHistoryDrivers = [];
        $teams = [];

        /** @var Racer $driver */
        foreach ($this->season->drivers as $driver) {
            $ratings = $this->getRatingHistory($driver);

            // Don't include drivers without any development history
            if (array_sum($ratings) === 0) {
                continue;
            }

            $teams[$driver->entrant_id][] = $driver->id;

            $developmentHistoryDrivers[] = DevelopmentHistoryEntity::fromDriver(
                racer: $driver,
                history: $ratings,
                dash: count($teams[$driver->entrant_id]) > 1,
            );
        }

        return $developmentHistoryDrivers;
    }

    private function setup(
        Season $season,
        Collection $races,
        string $component,
    ): void {
        $this->season = $season;
        $this->races = $races;
        $this->component = $component;
        $this->nextRace = $season->nextRace();

        $this->season->load([
            'drivers' => [
                'driver',
                'entrant',
                'developmentHistories',
            ],
        ]);
    }
}
