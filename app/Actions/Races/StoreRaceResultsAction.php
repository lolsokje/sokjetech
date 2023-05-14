<?php

namespace App\Actions\Races;

use App\DataTransferObjects\RaceWeekend\RaceDriver;
use App\Http\Requests\StoreRaceResultsRequest;
use App\Models\Race;
use App\Models\RaceResult;

class StoreRaceResultsAction
{
    public function __construct(protected StoreRaceResultsRequest $request, protected Race $race)
    {
    }

    public function handle(): void
    {
        $this->updateRaceDetails();

        foreach ($this->request->drivers() as $driver) {
            $this->updateRaceResults(new RaceDriver($driver));
        }
    }

    private function updateRaceDetails(): void
    {
        $updateArray = ['race_details' => $this->request->details()];

        if ($this->race->started) {
            $updateArray['started'] = true;
        }

        $this->race->update($updateArray);
    }

    private function updateRaceResults(RaceDriver $driver): void
    {
        RaceResult::updateOrCreate(
            ['race_id' => $this->race->id, 'racer_id' => $driver->id],
            [
                'driver_rating' => $driver->rating->driverRating,
                'team_rating' => $driver->rating->teamRating,
                'engine_rating' => $driver->rating->engineRating,
                'position' => $driver->result->position,
                'starting_position' => $driver->result->startingPosition,
                'starting_bonus' => $driver->result->startingBonus,
                'dnf' => $driver->result->dnf,
                'fastest_lap' => $driver->result->fastestLap,
                'fastest_lap_roll' => $driver->result->fastestLapRoll,
                'stints' => $driver->result->stints,
            ],
        );
    }
}
