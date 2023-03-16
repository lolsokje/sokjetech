<?php

namespace App\Actions\Season\Copy;

use App\Exceptions\InvalidSeasonRequirements;

class CopyEngines extends CopyWithRatingsAction
{
    protected function copyModels(): void
    {
        $this->oldSeason->engines->load('baseEngine');

        foreach ($this->oldSeason->engines as $engine) {
            $newEngine = $engine->replicate($this->columnsNotToCopy);
            $newEngine->baseEngine()->associate($engine->baseEngine);
            $newEngine->season()->associate($this->newSeason);

            $newEngine->save();
        }
    }

    protected function removeExistingModels(): void
    {
        $this->newSeason->engines()->delete();
        $this->newSeason->engines()->delete();
    }

    /**
     * @throws InvalidSeasonRequirements
     */
    protected function validateSeasonRequirementsMet(): void
    {
        if ($this->oldSeason->engines->count() === 0) {
            throw new InvalidSeasonRequirements('No engines added to the selected season');
        }
    }
}
