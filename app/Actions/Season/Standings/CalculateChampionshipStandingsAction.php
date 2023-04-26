<?php

namespace App\Actions\Season\Standings;

use App\Actions\CalculateTieBreaker;
use App\Models\Season;
use Illuminate\Support\Collection;

class CalculateChampionshipStandingsAction
{
    protected array $results = [];
    protected CalculateTieBreaker $action;
    protected Collection $models;

    public function __construct(
        protected Season $season,
        protected string $modelColumn,
    ) {
    }

    public function hasResults(): bool
    {
        return count($this->season->raceResults) > 0;
    }

    public function sortResults(): void
    {
        $this->action = new CalculateTieBreaker($this->season);

        uasort($this->results, fn (
            array $modelOne,
            array $modelTwo,
        ) => $this->performSort($modelOne, $modelTwo));
    }

    public function addPositionToResults(): void
    {
        $position = 1;

        foreach ($this->results as $key => $result) {
            $this->results[$key]['position'] = $position;
            $position++;
        }
    }

    private function performSort(array $modelOne, array $modelTwo): int
    {
        if ($modelOne['points'] === $modelTwo['points']) {
            return $this->action->handle(
                $this->models[$modelTwo[$this->modelColumn]],
                $this->models[$modelOne[$this->modelColumn]],
            );
        }

        return $modelOne['points'] > $modelTwo['points'] ? -1 : 1;
    }
}
