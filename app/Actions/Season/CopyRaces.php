<?php

namespace App\Actions\Season;

use App\Exceptions\InvalidSeasonRequirements;
use App\Http\Requests\CopyRaceSetupRequest;
use App\Models\Race;
use App\Models\Season;

class CopyRaces extends BaseCopyAction
{
    private Season $oldSeason;

    public function __construct(protected CopyRaceSetupRequest $request, protected Season $newSeason)
    {
        $this->oldSeason = Season::find($this->request->validated('season_id'));
    }

    public function handle(): void
    {
        $this->validateSeasonOwnership($this->oldSeason);
        $this->validateSeasonHasRaces();
        $this->clearExistingRaces();
        $this->copyRaces();
    }

    private function clearExistingRaces(): void
    {
        $this->newSeason->races()->each(fn (Race $race) => $race->stints()->delete());
        $this->newSeason->races()->delete();
    }

    private function copyRaces(): void
    {
        foreach ($this->oldSeason->races as $oldRace) {
            $newRace = $oldRace->replicate([
                'qualifying_started',
                'qualifying_completed',
                'started',
                'completed',
                'completed_at',
                'qualifying_details',
                'race_details',
            ]);
            $newRace->season()->associate($this->newSeason);
            $newRace->name = $this->getRaceName($oldRace);
            $newRace->save();

            if ($this->request->copyStints()) {
                $this->copyStints($oldRace, $newRace);
            }
        }
    }

    private function copyStints(Race $oldRace, Race $newRace): void
    {
        $oldRace->load('stints');
        foreach ($oldRace->stints as $oldStint) {
            $newStint = $oldStint->replicate();
            $newStint->race()->associate($newRace);
            $newStint->save();
        }
    }

    private function validateSeasonHasRaces(): void
    {
        if ($this->oldSeason->races->count() === 0) {
            throw new InvalidSeasonRequirements('No races added to the selected season');
        }
    }

    private function getRaceName(Race $oldRace): string
    {
        return str_replace($this->oldSeason->year, $this->newSeason->year, $oldRace->name);
    }
}
