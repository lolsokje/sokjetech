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
    ): void {
        $drivers->each(fn (QualifyingDriver $driver) => $this->updateOrCreateQualifyingResult(
            driver: $driver,
            raceId: $raceId,
        ));
    }

    private function updateOrCreateQualifyingResult(
        QualifyingDriver $driver,
        int $raceId,
    ): void {
        $result = QualifyingResult::query()
            ->where('racer_id', $driver->id)
            ->where('race_id', $raceId)
            ->first();

        $result->update([
            'position' => $driver->result->position,
            'runs' => $driver->result->sessions,
        ]);
    }
}
