<?php

namespace App\Actions\Races\Results;

use App\DataTransferObjects\RaceWeekend\QualifyingDriver;
use App\Models\QualifyingResult;
use Illuminate\Support\Collection;

class StoreQualifyingResultsAction
{
    /**
     * @param Collection<QualifyingDriver> $drivers
     * @param int $raceId
     * @param int $seasonId
     */
    public function handle(
        Collection $drivers,
        int $raceId,
        int $seasonId,
    ): void {
        $drivers->each(fn (QualifyingDriver $driver) => $this->updateOrCreateQualifyingResult(
            driver: $driver,
            raceId: $raceId,
            seasonId: $seasonId,
        ));
    }

    private function updateOrCreateQualifyingResult(
        QualifyingDriver $driver,
        int $raceId,
        int $seasonId,
    ): void {
        QualifyingResult::query()->updateOrCreate([
            'race_id' => $raceId,
            'racer_id' => $driver->id,
        ], [
            'position' => $driver->result->position,
            'runs' => $driver->result->sessions,
            'season_id' => $seasonId,
            'entrant_id' => $driver->entrantId,
            'driver_rating' => $driver->rating->driverRating,
            'team_rating' => $driver->rating->teamRating,
            'engine_rating' => $driver->rating->engineRating,
        ]);
    }
}
