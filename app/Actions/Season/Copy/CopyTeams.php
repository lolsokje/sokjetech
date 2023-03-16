<?php

namespace App\Actions\Season\Copy;

use App\Exceptions\InvalidSeasonRequirements;

class CopyTeams extends CopyWithRatingsAction
{
    protected function copyModels(): void
    {
        foreach ($this->oldSeason->entrants->load('activeRacers', 'engine') as $oldEntrant) {
            $newEntrant = $oldEntrant->replicate($this->columnsNotToCopy);
            $newEntrant->season()->associate($this->newSeason);
            $newEntrant->save();
        }
    }

    protected function removeExistingModels(): void
    {
        $this->newSeason->drivers()->delete();
        $this->newSeason->entrants()->delete();
    }

    /**
     * @throws InvalidSeasonRequirements
     */
    protected function validateSeasonRequirementsMet(): void
    {
        if ($this->oldSeason->entrants->count() === 0) {
            throw new InvalidSeasonRequirements('No entrants added to the selected season');
        }
    }
}
