<?php

namespace App\Actions\Season;

use App\Exceptions\InvalidSeasonRequirements;
use App\Http\Requests\CopyReliabilityConfigurationRequest;
use App\Models\Season;

class CopyReliabilityConfiguration extends BaseCopyAction
{
    private Season $oldSeason;

    public function __construct(protected CopyReliabilityConfigurationRequest $request, protected Season $newSeason)
    {
        $this->oldSeason = Season::find($this->request->validated('season_id'));
    }

    public function handle(): void
    {
        $this->validateSeasonOwnership($this->oldSeason);
        $this->validateSeasonRequirementsMet();
        $this->clearExistingConfiguration();
        $this->copyReliabilityConfiguration();
        $this->copyReliabilityReasons();
    }

    private function clearExistingConfiguration(): void
    {
        $this->newSeason->reliabilityConfiguration()?->delete();
        $this->newSeason->reliabilityReasons()->each(fn ($reason) => $reason->delete());
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

    private function validateSeasonRequirementsMet(): void
    {
        if (!$this->oldSeason->reliabilityConfiguration()->first() ||
            $this->oldSeason->reliabilityReasons()->count() === 0
        ) {
            throw new InvalidSeasonRequirements('No reliability configuration found');
        }
    }
}
