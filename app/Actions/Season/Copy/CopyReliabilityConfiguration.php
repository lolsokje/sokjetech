<?php

namespace App\Actions\Season\Copy;

use App\Exceptions\InvalidSeasonRequirements;

class CopyReliabilityConfiguration extends CopyAction
{
    protected function removeExistingModels(): void
    {
        $this->newSeason->reliabilityConfiguration()?->delete();
        $this->newSeason->reliabilityReasons()->each(fn ($reason) => $reason->delete());
    }

    protected function copyModels(): void
    {
        $this->copyReliabilityConfiguration();
        $this->copyReliabilityReasons();
    }

    private function copyReliabilityReasons(): void
    {
        foreach ($this->oldSeason->reliabilityReasons as $reason) {
            $newReason = $reason->replicate();
            $newReason->season()->associate($this->newSeason);
            $newReason->save();
        }
    }

    private function copyReliabilityConfiguration(): void
    {
        $newConfiguration = $this->oldSeason->reliabilityConfiguration->replicate();
        $newConfiguration->season()->associate($this->newSeason);
        $newConfiguration->save();
    }

    protected function validateSeasonRequirementsMet(): void
    {
        if (! $this->oldSeason->reliabilityConfiguration()->first() ||
            $this->oldSeason->reliabilityReasons()->count() === 0
        ) {
            throw new InvalidSeasonRequirements('No reliability configuration found');
        }
    }
}
