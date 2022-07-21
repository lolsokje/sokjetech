<?php

namespace App\Http\Controllers;

use App\Http\Resources\RaceResultResource;
use App\Http\Resources\RaceWeekendDriverResource;
use App\Models\Race;
use Inertia\Inertia;
use Inertia\Response;

class ShowRacePageController extends Controller
{
    public function __invoke(Race $race): Response
    {
        $this->authorize('view', $race->universe());

        $race->load([
            'stints',
            'season' => [
                'activeRacers' => [
                    'driver',
                    'entrant' => ['engine'],
                ],
            ],
            'raceResults',
        ]);

        $drivers = RaceWeekendDriverResource::collection($race->season->activeRacers);
        return Inertia::render('RaceWeekend/Race', [
            'race' => $race->load('season'),
            'drivers' => $drivers->toArray(request()),
            'raceResults' => RaceResultResource::collection($race->raceResults)->toArray(request()),
        ]);
    }
}
