<?php

namespace App\Actions;

use App\Models\RaceResult;
use App\Models\Season;
use Illuminate\Database\Eloquent\Model;

class CalculateTieBreaker
{
    private int $best;
    private int $worst;
    private array $positions = [];

    public function __construct(
        protected readonly Season $season,
    ) {
        $this->getLowestAndHighestFinishingPosition();
    }

    public function handle(
        Model $modelOne,
        Model $modelTwo,
    ): int {
        $modelOne->loadMissing('raceResults');
        $modelTwo->loadMissing('raceResults');

        $this->getFinishingPositionCounts($modelOne);
        $this->getFinishingPositionCounts($modelTwo);

        for ($position = $this->best; $position <= $this->worst; $position++) {
            $modelOneCount = $this->positions[$modelOne->id][$position] ?? 0;
            $modelTwoCount = $this->positions[$modelTwo->id][$position] ?? 0;

            if ($modelOneCount === $modelTwoCount) {
                continue;
            }

            return $modelOneCount > $modelTwoCount ? 1 : -1;
        }

        return 0;
    }

    private function getLowestAndHighestFinishingPosition(): void
    {
        $this->best = $this->season->raceResults->sortBy('position')->first()->position;
        $this->worst = $this->season->raceResults->sortByDesc('position')->first()->position;
    }

    private function getFinishingPositionCounts(Model $model): void
    {
        if (! in_array($model->id, $this->positions)) {
            $positions = $model->raceResults->map(fn (RaceResult $result) => $result->position)
                ->toArray();

            $this->positions[$model->id] = array_count_values($positions);
        }
    }
}
