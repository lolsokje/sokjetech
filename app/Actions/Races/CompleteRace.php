<?php

namespace App\Actions\Races;

use App\Models\Race;

class CompleteRace
{
    public function __construct(protected Race $race)
    {
    }

    public function handle(): void
    {
        $this->race->update([
            'completed' => true,
            'completed_at' => now(),
        ]);
    }
}
