<?php

namespace App\Actions\Races\Results;

use App\Models\Race;
use App\Models\Racer;

class CreateQualifyingResults
{
    public function handle(Race $race): void
    {
        $racers = Racer::query()
            ->where('season_id', $race->season_id)
            ->where('active', true)
            ->with('entrant.engine')
            ->get();

        foreach ($racers as $index => $racer) {
            $race->qualifyingResults()->create([
                'season_id' => $race->season_id,
                'entrant_id' => $racer->entrant_id,
                'racer_id' => $racer->id,
                'position' => $index + 1,
                'driver_rating' => $racer->rating,
                'team_rating' => $racer->entrant->rating,
                'engine_rating' => $racer->entrant->engine?->rating,
                'runs' => [1 => ['runs' => [], 'position' => $index + 1]],
            ]);
        }
    }
}
