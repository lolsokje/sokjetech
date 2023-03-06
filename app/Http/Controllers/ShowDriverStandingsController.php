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
            'pointSystem',
            'races' => fn (HasMany $query) => $query->orderBy('order')->with('circuit'),
            'driverChampionshipStandings' => function (HasMany $query) {
                $query->orderBy('position')->with([
                    'racer' => [
                        'driver',
                        'entrant',
                        'raceResults' => ['race'],
                    ],
                ]);
            },
        ]);

        return Inertia::render('Standings/Drivers', [
            'season' => SeasonStandingResource::make($season)->toArray(request()),
            'races' => $season->races,
            'drivers' => DriverStandingsResource::collection($season->driverChampionshipStandings)
                ->toArray(request()),
        ]);
    }
}
