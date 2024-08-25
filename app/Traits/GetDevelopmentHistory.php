<?php

declare(strict_types=1);

namespace App\Traits;

use App\Contracts\Development\HasDevelopmentHistory;
use App\Contracts\Development\IsDevelopmentEntity;
use App\Models\Race;
use App\Models\Season;
use Illuminate\Support\Collection;

trait GetDevelopmentHistory
{
    private Season $season;

    /** @var Collection<Race> */
    private Collection $races;

    private Race $nextRace;

    private string $component;

    public function getRatingHistory(
        IsDevelopmentEntity $entity,
    ): array {
        $ratings = [];

        foreach ($this->races as $race) {
            $histories = $this->getDevelopmentHistoryForEntity($entity);

            if (! $histories->has($race->id)) {
                $ratings[] = $this->getLastRating(
                    race: $race,
                    ratings: $ratings,
                    initial: $this->getFirstRatingForEntity($histories),
                );

                continue;
            }

            $ratings[] = $this->getRatingForRace($race, $histories);
        }

        return $ratings;
    }

    private function getLastRating(
        Race $race,
        array $ratings,
        int $initial,
    ): ?int {
        $include = $race->order <= $this->nextRace->order;

        $lastRating = null;

        if ($include) {
            $lastRating = $ratings[array_key_last($ratings)] ?? $initial;
        }

        return $lastRating;
    }

    private function getRatingForRace(
        Race $race,
        Collection $histories,
    ): int {
        /** @var HasDevelopmentHistory $history */
        $history = $histories->get($race->id);

        return $history->getInitialRating() + $history->getCurrentDevelopment();
    }

    private function getDevelopmentHistoryForEntity(
        IsDevelopmentEntity $entity,
    ): Collection {
        return $entity
            ->developmentHistories
            ->where('season_id', $this->season->id)
            ->where('component', $this->component)
            ->mapWithKeys(fn(HasDevelopmentHistory $history) => [$history->raceId() => $history]);
    }

    /**
     * @param Collection<HasDevelopmentHistory> $histories
     */
    private function getFirstRatingForEntity(
        Collection $histories,
    ): int {
        /** @var HasDevelopmentHistory $firstRecord */
        $firstRecord = $histories->first();

        return $firstRecord ? $firstRecord->getInitialRating() : 0;
    }
}
