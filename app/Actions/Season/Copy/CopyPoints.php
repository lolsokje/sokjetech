<?php

namespace App\Actions\Season\Copy;

use App\Exceptions\InvalidSeasonRequirements;
use App\Models\PointSystem;

class CopyPoints extends CopyAction
{
    protected function removeExistingModels(): void
    {
        $this->newSeason->pointDistribution()->delete();
        $this->newSeason->pointSystem()?->delete();
    }

    protected function copyModels(): void
    {
        $newSystem = $this->oldSeason->pointSystem->replicate();
        $newSystem->season()->associate($this->newSeason);
        $newSystem->save();

        $this->copyPointDistributions($newSystem);
    }

    protected function validateSeasonRequirementsMet(): void
    {
        if ($this->oldSeason->pointSystem === null) {
            throw new InvalidSeasonRequirements('No point system added to the selected season');
        }

        if ($this->oldSeason->pointDistribution->count() === 0) {
            throw new InvalidSeasonRequirements('No point distribution configured for the selected season');
        }
    }

    private function copyPointDistributions(PointSystem $newSystem): void
    {
        foreach ($this->oldSeason->pointSystem->pointDistributions as $oldDistribution) {
            $newDistribution = $oldDistribution->replicate();
            $newDistribution->pointSystem()->associate($newSystem);
            $newDistribution->save();
        }
    }
}
