<?php

namespace App\Actions\Season;

use App\Exceptions\InvalidSeasonRequirements;

class CopyQualifyingFormat extends BaseCopyAction
{
    /**
     * @throws InvalidSeasonRequirements
     */
    public function handle(): void
    {
        $this->validateSeasonOwnership($this->oldSeason);
        $this->validateSeasonHasQualifyingFormat();
        $this->removeExistingFormatFromNewSeason();
        $this->copyQualifyingFormat();
    }

    private function removeExistingFormatFromNewSeason(): void
    {
        $this->newSeason->qualifyingFormat?->delete();
    }

    private function copyQualifyingFormat(): void
    {
        $newFormat = $this->oldSeason->qualifyingFormat->replicate();
        $newFormat->save();
        $newFormat->season()->save($this->newSeason);
    }

    private function validateSeasonHasQualifyingFormat(): void
    {
        if ($this->oldSeason->qualifyingFormat === null) {
            throw new InvalidSeasonRequirements('No qualifying format configured for the selected season');
        }
    }
}
