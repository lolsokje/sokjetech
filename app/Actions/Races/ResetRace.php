<?php

namespace App\Actions\Races;

use App\Models\Race;

class ResetRace
{
    public function handle(Race $race): void
    {
        $race->raceResults()->delete();
        $race->qualifyingResults()->delete();

        $race->update([
            'qualifying_started' => false,
            'qualifying_completed' => false,
            'started' => false,
            'completed' => false,
            'qualifying_details' => null,
            'race_details' => null,
        ]);
    }
}
