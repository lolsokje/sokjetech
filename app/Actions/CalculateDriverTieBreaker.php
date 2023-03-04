<?php

namespace App\Actions;

use App\Models\Racer;
use App\Models\RaceResult;
use App\Models\Season;

class CalculateDriverTieBreaker
{
    private int $best;
    private int $worst;
    private array $positions = [];

    public function __construct(
        protected readonly Season $season,
    ) {
        $this->getLowestAndHighestFinishingPosition();
    }

    public function handle(
        Racer $driverOne,
        Racer $driverTwo,
    ): int {
        $driverOne->loadMissing('raceResults');
        $driverTwo->loadMissing('raceResults');

        $this->getDriverFinishingPositionCounts($driverOne);
        $this->getDriverFinishingPositionCounts($driverTwo);

        for ($position = $this->best; $position <= $this->worst; $position++) {
            $driverOneCount = $this->positions[$driverOne->id][$position] ?? 0;
            $driverTwoCount = $this->positions[$driverTwo->id][$position] ?? 0;

            if ($driverOneCount === $driverTwoCount) {
                continue;
            }

            return $driverOneCount > $driverTwoCount ? 1 : -1;
        }

        return 0;
    }

    private function getLowestAndHighestFinishingPosition(): void
    {
        $this->best = $this->season->raceResults->sortBy('position')->first()->position;
        $this->worst = $this->season->raceResults->sortByDesc('position')->first()->position;
    }

    private function getDriverFinishingPositionCounts(Racer $driver): void
    {
        if (! in_array($driver->id, $this->positions)) {
            $positions = $driver->raceResults->map(fn (RaceResult $result) => $result->position)
                ->toArray();

            $this->positions[$driver->id] = array_count_values($positions);
        }
    }
}
