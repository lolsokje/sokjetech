<?php

namespace App\Http\Controllers;

use App\Http\Resources\DriverStandingsResource;
use App\Http\Resources\SeasonStandingResource;
use App\Models\Season;
use Inertia\Inertia;
use Inertia\Response;

class ShowDriverStandingsController extends Controller
{
    public function __invoke(Season $season): Response
    {
        $season->load([
            'pointDistribution',
            'races' => ['circuit'],
            'driversWithParticipation' => [
                'driver',
                'entrant',
                'raceResults' => ['race'],
            ],
        ]);

        return Inertia::render('Standings/Drivers', [
            'season' => SeasonStandingResource::make($season)->toArray(request()),
            'races' => $season->races,
            'drivers' => DriverStandingsResource::collection($season->driversWithParticipation)->toArray(request()),
        ]);
    }
}
