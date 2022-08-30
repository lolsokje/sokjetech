<?php

namespace App\Http\Controllers;

use App\Http\Resources\GeneralSeasonResource;
use App\Http\Resources\TeamStandingsResource;
use App\Models\Season;
use Inertia\Inertia;

class ShowTeamStandingsController extends Controller
{
    public function __invoke(Season $season)
    {
        $season->load([
            'races' => ['circuit'],
            'entrants' => [
                'racersWithParticipation',
                'raceResults' => [
                    'race',
                    'racer' => [
                        'driver',
                    ],
                ],
            ],
        ]);

        return Inertia::render('Standings/Teams', [
            'season' => GeneralSeasonResource::make($season)->toArray(request()),
            'races' => $season->races,
            'teams' => TeamStandingsResource::collection($season->entrants)->toArray(request()),
        ]);
    }
}
