<?php

namespace App\Actions\Season;

use App\Exceptions\InvalidSeasonRequirements;

class CopyTeams extends BaseCopyAction
{
    /**
     * @throws InvalidSeasonRequirements
     */
    public function handle(): void
    {
        $this->validateSeasonRequirementsMet();
        $this->clearExistingTeams();
        $this->copyTeams();
    }

    private function copyTeams(): void
    {
        foreach ($this->oldSeason->entrants->load('activeRacers', 'engine') as $oldEntrant) {
            $newEntrant = $oldEntrant->replicate($this->columnsNotToCopy);
            $newEntrant->season()->associate($this->newSeason);
            $newEntrant->save();
        }
    }

    private function clearExistingTeams(): void
    {
        $this->newSeason->drivers()->delete();
        $this->newSeason->entrants()->delete();
    }

    /**
     * @throws InvalidSeasonRequirements
     */
    private function validateSeasonRequirementsMet(): void
    {
        if ($this->oldSeason->entrants->count() === 0) {
            throw new InvalidSeasonRequirements('No entrants added to the selected season');
        }
    }
}
