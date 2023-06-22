<?php

namespace App\Actions\Races;

use App\Models\Race;
use App\Models\Season;

class DeleteRace
{
    public function __construct(protected Season $season, protected Race $race)
    {
    }

    public function handle(): void
    {
        $this->deleteRace();
        $this->reorderRaces();
    }

    private function deleteRace(): void
    {
        $this->race->delete();
    }

    private function reorderRaces(): void
    {
        foreach ($this->season->races as $key => $race) {
            $race->update(['order' => $key + 1]);
        }
    }
}
