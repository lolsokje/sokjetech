<?php

namespace App\Http\Controllers;

use App\Http\Resources\DriverStandingsResource;
use App\Http\Resources\SeasonStandingResource;
use App\Models\Season;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Inertia\Inertia;
use Inertia\Response;

class ShowDriverStandingsController extends Controller
{
    public function __invoke(Season $season): Response
    {
        $season->load([
            'pointDistribution',
            'races' => function (HasMany $query) {
                $query->orderBy('order')->with('circuit');
            },
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
