<?php

namespace App\Actions\Season;

use App\Exceptions\InvalidSeasonRequirements;

class CopyDrivers extends BaseCopyAction
{
    /**
     * @throws InvalidSeasonRequirements
     */
    public function handle()
    {
        $this->validateSeasonRequirementsMet();
        $this->clearExistingDrivers();
        $this->copyDrivers();
    }

    private function clearExistingDrivers(): void
    {
        $this->newSeason->drivers()->delete();
    }

    private function copyDrivers(): void
    {
        $this->newSeason->load('entrants');
        $this->oldSeason->drivers->load('driver', 'entrant');

        foreach ($this->oldSeason->drivers->load('driver') as $driver) {
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
    private function validateSeasonRequirementsMet(): void
    {
        if ($this->oldSeason->drivers->count() === 0) {
            throw new InvalidSeasonRequirements('No drivers added to the selected season');
        }
    }
}
