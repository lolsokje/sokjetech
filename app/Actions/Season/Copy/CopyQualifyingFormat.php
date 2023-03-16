<?php

namespace App\Actions\Season\Copy;

use App\Exceptions\InvalidSeasonRequirements;

class CopyQualifyingFormat extends CopyAction
{
    protected function removeExistingModels(): void
    {
        $formatId = $this->newSeason->format_id;
        $formatType = $this->newSeason->format_type;


        if (! $formatId || ! $formatType) {
            return;
        }

        $formatType::query()->find($formatId)?->delete();
    }

    protected function copyModels(): void
    {
        $newFormat = $this->oldSeason->qualifyingFormat->replicate();
        $newFormat->save();
        $newFormat->season()->save($this->newSeason);
    }

    protected function validateSeasonRequirementsMet(): void
    {
        if ($this->oldSeason->qualifyingFormat === null) {
            throw new InvalidSeasonRequirements('No qualifying format configured for the selected season');
        }
    }
}
