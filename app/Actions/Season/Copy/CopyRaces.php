<?php

namespace App\Actions\Season\Copy;

use App\Exceptions\InvalidSeasonRequirements;
use App\Models\Race;

class CopyRaces extends CopyAction
{
    protected ?array $columnsNotToCopy = [
        'qualifying_started',
        'qualifying_completed',
        'started',
        'completed',
        'completed_at',
        'qualifying_details',
        'race_details',
    ];

    private CopyStintsAction $copyStintsAction;

    /**
     * @throws InvalidSeasonRequirements
     */
    public function handle(
        ?bool $copyStints = false,
    ): void {
        $this->copyStintsAction = new CopyStintsAction($copyStints);

        $this->validateSeasonRequirementsMet();
        $this->removeExistingModels();
        $this->copyModels();
    }

    protected function removeExistingModels(): void
    {
        $this->newSeason->races()->each(fn (Race $race) => $race->stints()->delete());
        $this->newSeason->races()->delete();
    }

    protected function copyModels(): void
    {
        $this->oldSeason->load('races.stints');

        foreach ($this->oldSeason->races as $oldRace) {
            $newRace = $oldRace->replicate($this->columnsNotToCopy);
            $newRace->season()->associate($this->newSeason);
            $newRace->name = $this->getRaceName($oldRace);
            $newRace->save();

            $this->copyStintsAction->handle($oldRace, $newRace);
        }
    }

    protected function validateSeasonRequirementsMet(): void
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
