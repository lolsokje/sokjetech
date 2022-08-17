<?php

namespace App\Actions\Season;

use App\Exceptions\InvalidSeasonRequirements;
use App\Http\Requests\CopyQualifyingFormatRequest;
use App\Models\Season;

class CopyQualifyingFormat extends BaseCopyAction
{
    protected Season $oldSeason;

    public function __construct(protected CopyQualifyingFormatRequest $request, protected Season $newSeason)
    {
        $this->oldSeason = Season::find($this->request->validated('season_id'));
    }

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
