<?php

namespace App\Actions\Season\Copy;

use App\Exceptions\InvalidSeasonRequirements;
use App\Models\Season;

class CopyTeamsAndEngines extends CopyWithRatingsAction
{
    private array $engines = [];
    private CopyEngines $copyEnginesAction;

    public function __construct(
        Season $oldSeason,
        Season $newSeason,
    ) {
        parent::__construct($oldSeason, $newSeason);

        $this->copyEnginesAction = new CopyEngines(
            $oldSeason,
            $newSeason,
        );
    }

    protected function copyModels(): void
    {
        $this->copyEnginesAction->handle($this->columnsNotToCopy);

        foreach ($this->oldSeason->entrants->load('activeRacers', 'engine') as $oldEntrant) {
            $newEntrant = $oldEntrant->replicate($this->columnsNotToCopy);
            $newEntrant->season()->associate($this->newSeason);
            $newEntrant->save();

            $this->copyEnginesAction->copyEngineToEntrant($oldEntrant, $newEntrant);
        }
    }

    protected function removeExistingModels(): void
    {
        $this->newSeason->drivers()->delete();
        $this->newSeason->entrants()->delete();
    }

    /**
     * @throws InvalidSeasonRequirements
     */
    protected function validateSeasonRequirementsMet(): void
    {
        if ($this->oldSeason->entrants->count() === 0) {
            throw new InvalidSeasonRequirements('No entrants added to the selected season');
        }
    }
}
