<?php

namespace App\Actions\Season;

use App\Exceptions\InvalidSeasonRequirements;
use App\Http\Requests\CopyPointsSystemRequest;
use App\Models\PointDistribution;
use App\Models\PointSystem;
use App\Models\Season;

class CopyPoints extends BaseCopyAction
{
    protected Season $oldSeason;

    public function __construct(protected CopyPointsSystemRequest $request, protected Season $newSeason)
    {
        $this->oldSeason = Season::find($this->request->validated('season_id'));
    }

    /**
     * @throws InvalidSeasonRequirements
     */
    public function handle(): void
    {
        $this->validateSeasonOwnership($this->oldSeason);
        $this->validateSeasonRequirementsMet();
        $this->removeExistingPointSystem();
        $this->copyPointSystem();
    }

    private function removeExistingPointSystem(): void
    {
        $this->newSeason
            ->pointSystem
            ?->pointDistributions
            ->each(fn (PointDistribution $distribution) => $distribution->delete());
        $this->newSeason->pointSystem?->delete();
    }

    private function copyPointSystem(): void
    {
        $newSystem = $this->oldSeason->pointSystem->replicate();
        $newSystem->season()->associate($this->newSeason);
        $newSystem->save();

        $this->copyPointDistributions($newSystem);
    }

    private function copyPointDistributions(PointSystem $newSystem): void
    {
        foreach ($this->oldSeason->pointSystem->pointDistributions as $oldDistribution) {
            $newDistribution = $oldDistribution->replicate();
            $newDistribution->pointSystem()->associate($newSystem);
            $newDistribution->save();
        }
    }

    private function validateSeasonRequirementsMet(): void
    {
        if ($this->oldSeason->pointSystem === null) {
            throw new InvalidSeasonRequirements('No point system added to the selected season');
        }

        if ($this->oldSeason->pointDistribution->count() === 0) {
            throw new InvalidSeasonRequirements('No point distribution configured for the selected season');
        }
    }
}
