<?php

namespace App\Actions\Races;

use App\DataTransferObjects\RaceWeekend\QualifyingDriver;
use App\Models\QualifyingResult;
use App\Models\Race;
use Illuminate\Support\Collection;

class StoreQualifyingResultsAction
{
    public function __construct(protected array $details, protected Collection $drivers, protected Race $race)
    {
    }

    public function handle(): void
    {
        $this->updateQualifyingDetails();

        foreach ($this->drivers as $driver) {
            $this->updateOrCreateQualifyingResult(new QualifyingDriver($driver));
        }
    }

    private function updateQualifyingDetails(): void
    {
        $updateArray = ['qualifying_details' => $this->details];

        if (! $this->race->qualifying_started) {
            $updateArray['qualifying_started'] = true;
        }

        $this->race->update($updateArray);
    }

    private function updateOrCreateQualifyingResult(QualifyingDriver $driver): void
    {
        QualifyingResult::updateOrCreate(
            [
                'race_id' => $this->race->id,
                'racer_id' => $driver->id,
            ],
            [
                'position' => $driver->result->position,
                'runs' => $driver->result->sessions,
                'season_id' => $this->race->season_id,
                'entrant_id' => $driver->entrantId,
                'driver_rating' => $driver->rating->driverRating,
                'team_rating' => $driver->rating->teamRating,
                'engine_rating' => $driver->rating->engineRating,
            ],
        );
    }
}
