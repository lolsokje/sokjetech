<?php

namespace App\Actions\Races\Results;

use App\Models\Race;

final readonly class UpdateRaceDetails
{
    public function handle(Race $race, int $currentLap): void
    {
        $race->update([
            'current_lap' => $currentLap,
            'started' => true,
        ]);
    }
}
