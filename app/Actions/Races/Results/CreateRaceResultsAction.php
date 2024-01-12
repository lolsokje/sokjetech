<?php

namespace App\Actions\Races\Results;

use App\Models\QualifyingResult;
use App\Models\Race;

class CreateRaceResultsAction
{
    private int $bonusPerPosition = 3;
    private int $maxStartingBonus;

    public function handle(Race $race): void
    {
        $race->raceResults()->delete();

        $this->maxStartingBonus = $race->qualifyingResults->count() * $this->bonusPerPosition;

        $race->qualifyingResults()->each(function (QualifyingResult $result) use ($race) {
            $startingBonus = $this->getStartingBonus($result->position);

            $race->raceResults()->create([
                'racer_id' => $result->racer_id,
                'entrant_id' => $result->entrant_id,
                'season_id' => $result->season_id,
                'driver_rating' => $result->driver_rating,
                'team_rating' => $result->team_rating,
                'engine_rating' => $result->engine_rating,
                'starting_bonus' => $startingBonus,
                'starting_position' => $result->position,
                'position' => $result->position,
                'total' => $result->driver_rating + $result->team_rating + $result->engine_rating + $startingBonus,
            ]);
        });
    }

    private function getStartingBonus(int $startingPosition): int
    {
        return $this->maxStartingBonus - ($startingPosition * $this->bonusPerPosition) + $this->bonusPerPosition;
    }
}
