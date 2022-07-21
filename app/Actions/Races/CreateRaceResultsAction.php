<?php

namespace App\Actions\Races;

use App\Models\QualifyingResult;
use App\Models\Race;

class CreateRaceResultsAction
{
    public function __construct(protected Race $race)
    {
    }

    public function handle(): void
    {
        $this->race->raceResults()->delete();

        $this->race->qualifyingResults()->each(function (QualifyingResult $result) {
            $this->race->raceResults()->create([
                'racer_id' => $result->racer_id,
                'entrant_id' => $result->entrant_id,
                'season_id' => $result->season_id,
                'starting_position' => $result->position,
                'position' => $result->position,
            ]);
        });
    }
}
