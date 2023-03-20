<?php

namespace App\Actions\Season\Copy;

use App\Exceptions\InvalidSeasonRequirements;

class CopyDrivers extends CopyWithRatingsAction
{
    protected function removeExistingModels(): void
    {
        $this->newSeason->drivers()->delete();
    }

    protected function copyModels(): void
    {
        $this->newSeason->load('entrants');
        $this->oldSeason->drivers->load('driver', 'entrant');

        foreach ($this->oldSeason->drivers->load('driver') as $driver) {
            if ($driver->driver->retired) {
                continue;
            }

            $newDriver = $driver->replicate($this->columnsNotToCopy);
            $newEntrant = $this->newSeason->entrants->where('team_id', $driver->entrant->team_id)->first();
            $newDriver->entrant()->associate($newEntrant);
            $newDriver->season()->associate($this->newSeason);

            $newDriver->save();
        }
    }

    /**
     * @throws InvalidSeasonRequirements
     */
    protected function validateSeasonRequirementsMet(): void
    {
        if ($this->oldSeason->drivers->count() === 0) {
            throw new InvalidSeasonRequirements('No drivers added to the selected season');
        }
    }
}
