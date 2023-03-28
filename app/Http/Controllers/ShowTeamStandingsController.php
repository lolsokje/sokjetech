<?php

namespace App\Http\Controllers;

use App\Http\Resources\SeasonStandingResource;
use App\Http\Resources\TeamStandingsResource;
use App\Models\Season;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Inertia\Inertia;

class ShowTeamStandingsController extends Controller
{
    public function __invoke(Season $season)
    {
        $this->eagerLoadSeason($season);

        return Inertia::render('Standings/Teams', [
            'season' => SeasonStandingResource::make($season)->toArray(request()),
            'series' => $season->series,
            'races' => $season->races,
            'teams' => TeamStandingsResource::collection($season->teamChampionshipStandings)->toArray(request()),
        ]);
    }

    private function eagerLoadSeason(Season $season): void
    {
        $season->load([
            'pointDistribution',
            'races' => fn (HasMany $query) => $query->orderBy('order')->with('circuit'),
            'teamChampionshipStandings' => function (HasMany $query) {
                $query->orderBy('position')->with([
                    'entrant' => [
                        'racersWithParticipation',
                        'raceResults' => [
                            'race',
                            'racer' => ['driver'],
                        ],
                    ],
                ]);
            },
        ]);
    }
}
