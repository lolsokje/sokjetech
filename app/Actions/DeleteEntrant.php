<?php

namespace App\Actions;

use App\Models\Entrant;
use App\Models\Racer;

class DeleteEntrant
{
    public function __construct(protected Entrant $entrant)
    {
    }

    public function handle(): void
    {
        $this->deleteRacersFromEntrant();
        $this->entrant->delete();
    }

    private function deleteRacersFromEntrant(): void
    {
        $this->entrant->allRacers()->each(fn (Racer $racer) => $racer->delete());
    }
}
