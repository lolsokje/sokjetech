<?php

namespace App\Http\Controllers;

use App\Http\Resources\DriverStandingsResource;
use App\Models\Season;
use Inertia\Inertia;
use Inertia\Response;

class ShowDriverStandingsController extends Controller
{
    public function __invoke(Season $season): Response
    {
        $season->load([
            'races' => ['circuit'],
            'drivers' => [
                'driver',
                'entrant',
                'raceResults' => ['race'],
            ],
        ]);

        return Inertia::render('Standings/Drivers', [
            'season' => $season,
            'drivers' => DriverStandingsResource::collection($season->drivers)->toArray(request()),
        ]);
    }
}
