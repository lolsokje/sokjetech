<?php

namespace App\Http\Controllers;

use App\Http\Resources\DriverStandingsResource;
use App\Http\Resources\GeneralSeasonResource;
use App\Models\Season;
use Inertia\Inertia;
use Inertia\Response;

class ShowDriverStandingsController extends Controller
{
    public function __invoke(Season $season): Response
    {
        $season->load([
            'races' => ['circuit'],
            'driversWithParticipation' => [
                'driver',
                'entrant',
                'raceResults' => ['race'],
            ],
        ]);

        return Inertia::render('Standings/Drivers', [
            'season' => GeneralSeasonResource::make($season)->toArray(request()),
            'races' => $season->races,
            'drivers' => DriverStandingsResource::collection($season->driversWithParticipation)->toArray(request()),
        ]);
    }
}
