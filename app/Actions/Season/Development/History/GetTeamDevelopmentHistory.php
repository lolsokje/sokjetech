<?php

declare(strict_types=1);

namespace App\Actions\Season\Development\History;

use App\Contracts\Development\ReturnsDevelopmentHistory;
use App\Models\Season;
use App\Traits\GetDevelopmentHistory;
use App\ValueObjects\Season\Development\DevelopmentHistoryEntity;
use Illuminate\Support\Collection;

class GetTeamDevelopmentHistory implements ReturnsDevelopmentHistory
{
    use GetDevelopmentHistory;

    public function handle(
        Season $season,
        Collection $races,
        string $component,
    ): array {
        $this->setup(
            season: $season,
            races: $races,
            component: $component,
        );

        $developmentHistoryTeams = [];

        foreach ($this->season->entrants as $team) {
            $ratings = $this->getRatingHistory($team);

            // Don't include teams without any development history
            if (array_sum($ratings) === 0) {
                continue;
            }

            $developmentHistoryTeams[] = DevelopmentHistoryEntity::fromTeam(
                team: $team,
                history: $ratings,
            );
        }

        return $developmentHistoryTeams;
    }

    private function setup(
        Season $season,
        Collection $races,
        string $component,
    ): void {
        $this->season = $season;
        $this->races = $races;
        $this->component = $component;
        $this->nextRace = $season->nextRace();

        $this->season->load([
            'entrants.developmentHistories',
        ]);
    }
}
