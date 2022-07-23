<?php

namespace App\Actions\Races;

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
        $drivers = $this->request->drivers();

        if (!$this->race->started) {
            $this->race->update(['started' => true]);
        }

        $drivers->each(function (array $driver) {
            RaceResult::updateOrCreate(
                ['race_id' => $this->race->id, 'racer_id' => $driver['id']],
                [
                    'stints' => $driver['stints'],
                    'position' => $driver['position'],
                    'starting_bonus' => $driver['starting_bonus'],
                    'driver_rating' => $driver['driver_rating'],
                    'team_rating' => $driver['team_rating'],
                    'engine_rating' => $driver['engine_rating'],
                    'dnf' => $driver['dnf'],
                    'fastest_lap_roll' => $driver['fastest_lap_roll'] ?? null,
                    'fastest_lap' => $driver['fastest_lap'] ?? false,
                ],
            );
        });
    }
}
