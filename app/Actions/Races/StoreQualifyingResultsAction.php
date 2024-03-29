<?php

namespace App\Actions\Races;

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
            $this->updateOrCreateQualifyingResult($driver);
        }
    }

    private function updateQualifyingDetails(): void
    {
        $updateArray = ['qualifying_details' => $this->details];

        if (!$this->race->qualifying_started) {
            $updateArray['qualifying_started'] = true;
        }

        $this->race->update($updateArray);
    }

    private function updateOrCreateQualifyingResult(array $driver): void
    {
        QualifyingResult::updateOrCreate(
            [
                'race_id' => $this->race->id,
                'racer_id' => $driver['id'],
            ],
            [
                'position' => $driver['position'],
                'runs' => $driver['runs'],
                'season_id' => $this->race->season_id,
                'entrant_id' => $driver['entrant_id'],
                'driver_rating' => $driver['driver_rating'],
                'team_rating' => $driver['team_rating'],
                'engine_rating' => $driver['engine_rating'],
            ],
        );
    }
}
