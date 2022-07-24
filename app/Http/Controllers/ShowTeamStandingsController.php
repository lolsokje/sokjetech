<?php

namespace App\Http\Controllers;

use App\Http\Resources\TeamStandingsResource;
use App\Models\Season;
use Inertia\Inertia;

class ShowTeamStandingsController extends Controller
{
    public function __invoke(Season $season)
    {
        $season->load([
            'races',
            'entrants' => [
                'allRacers',
                'raceResults' => [
                    'race',
                    'racer' => [
                        'driver',
                    ],
                ],
            ],
        ]);

        return Inertia::render('Standings/Teams', [
            'season' => $season,
            'teams' => TeamStandingsResource::collection($season->entrants)->toArray(request()),
        ]);
    }
}
