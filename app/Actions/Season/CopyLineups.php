<?php

namespace App\Actions\Season;

use App\Exceptions\InvalidSeasonRequirements;
use App\Http\Requests\CopyTeamSetupRequest;
use App\Models\EngineSeason;
use App\Models\Entrant;
use App\Models\Season;

class CopyLineups extends BaseCopyAction
{
    protected Season $oldSeason;
    private ?array $columnsNotToCopy;

    public function __construct(protected CopyTeamSetupRequest $request, protected Season $newSeason)
    {
        $this->oldSeason = Season::find($request->validated('season_id'));
        $copyRatings = $request->validated('copy_ratings');
        $this->columnsNotToCopy = $copyRatings ? null : ['rating', 'reliability'];
    }

    /**
     * @throws InvalidSeasonRequirements
     */
    public function handle(): void
    {
        $this->validateSeasonOwnership($this->oldSeason);
        $this->validateSeasonRequirementsMet();
        $this->clearExistingLineups();
        $this->copyEngines();
        $this->copyTeams();
    }

    private function copyEngines(): void
    {
        foreach ($this->oldSeason->engines as $engine) {
            $newEngine = $engine->replicate($this->columnsNotToCopy);
            $newEngine->season()->associate($this->newSeason);
            $newEngine->save();
        }
    }

    private function copyTeams(): void
    {
        foreach ($this->oldSeason->entrants->load('activeRacers', 'engine') as $oldEntrant) {
            $newEntrant = $oldEntrant->replicate($this->columnsNotToCopy);
            $newEntrant->season()->associate($this->newSeason);
            $newEntrant->engine()->associate($this->getNewEngineToAssignToNewEntrant($oldEntrant));
            $newEntrant->save();

            $this->copyRacers($oldEntrant, $newEntrant);
        }
    }

    private function copyRacers(Entrant $oldEntrant, Entrant $newEntrant): void
    {
        foreach ($oldEntrant->activeRacers as $racer) {
            $newRacer = $racer->replicate($this->columnsNotToCopy);
            $newRacer->entrant()->associate($newEntrant);
            $newRacer->season()->associate($this->newSeason);
            $newRacer->save();
        }
    }

    private function clearExistingLineups(): void
    {
        $this->newSeason->drivers()->delete();
        $this->newSeason->entrants()->delete();
        $this->newSeason->engines()->delete();
    }

    private function getNewEngineToAssignToNewEntrant(Entrant $oldEntrant): ?EngineSeason
    {
        return $this->newSeason->engines->firstWhere('base_engine_id', $oldEntrant->engine?->base_engine_id);
    }

    private function validateSeasonRequirementsMet(): void
    {
        if ($this->oldSeason->entrants->count() === 0) {
            throw new InvalidSeasonRequirements('No entrants added to the selected season');
        }

        if ($this->oldSeason->drivers->count() === 0) {
            throw new InvalidSeasonRequirements('No drivers added to the selected season');
        }
    }
}
