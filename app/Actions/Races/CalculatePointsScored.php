<?php

namespace App\Actions\Races;

use App\Models\PointSystem;
use App\Models\Race;
use App\Models\RaceResult;

class CalculatePointsScored
{
    protected PointSystem $pointSystem;
    protected array $points;
    protected bool $polePositionPointAwarded;
    protected bool $fastestLapPointAwarded;
    protected int $fastestLapPointAmount;
    protected int $polePositionPointsAmount;

    public function __construct(protected Race $race)
    {
        $this->race->load([
            'raceResults',
            'season' => [
                'pointDistribution',
                'pointSystem',
            ],
        ]);

        $this->initialisePoints();

        $this->pointSystem = $this->race->season->pointSystem;
        $this->polePositionPointAwarded = $this->pointSystem->pole_position_point_awarded;
        $this->polePositionPointsAmount = $this->pointSystem->pole_position_point_amount;
        $this->fastestLapPointAwarded = $this->pointSystem->fastest_lap_point_awarded;
        $this->fastestLapPointAmount = $this->pointSystem->fastest_lap_point_amount;
    }

    public function handle(): void
    {
        $this->race->raceResults->each(function (RaceResult $result) {
            $points = $this->getPolePositionPoints($result);

            // if a driver has DNFd, no more points are awarded
            if ($result->dnf) {
                return $this->storePointsForResult($result, $points);
            }

            $points += $this->getFastestLapPoints($result);
            $points += $this->getRaceResultPoints($result);

            return $this->storePointsForResult($result, $points);
        });
    }

    private function getPolePositionPoints(RaceResult $result): int
    {
        if ($this->polePositionPointAwarded && $result->starting_position === 1) {
            return $this->polePositionPointsAmount;
        }
        return 0;
    }

    private function getFastestLapPoints(RaceResult $result): int
    {
        if ($this->fastestLapPointAwarded && $result->fastest_lap) {
            return $this->fastestLapPointAmount;
        }
        return 0;
    }

    private function getRaceResultPoints(RaceResult $result): int
    {
        return $this->points[$result->position] ?? 0;
    }

    private function storePointsForResult(RaceResult $result, int $points): bool
    {
        return $result->update(['points' => $points]);
    }

    private function initialisePoints(): void
    {
        foreach ($this->race->season->points() as $points) {
            $this->points[$points['position']] = $points['points'];
        }
    }
}
