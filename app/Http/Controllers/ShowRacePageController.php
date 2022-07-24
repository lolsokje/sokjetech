<?php

namespace App\Http\Controllers;

use App\Http\Resources\RaceResultResource;
use App\Http\Resources\RaceWeekendDriverResource;
use App\Models\Race;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ShowRacePageController extends Controller
{
    public function __invoke(Race $race): Response|RedirectResponse
    {
        if (!$race->qualifying_completed) {
            return to_route('weekend.qualifying', [$race]);
        }

        $this->authorize('view', $race->universe());

        $this->eagerLoadRace($race);

        $drivers = RaceWeekendDriverResource::collection($race->season->activeRacers);
        $pointSystem = $race->season->pointSystem;
        return Inertia::render('RaceWeekend/Race', [
            'race' => $race->load('season'),
            'drivers' => $drivers->toArray(request()),
            'raceResults' => RaceResultResource::collection($race->raceResults)->toArray(request()),
            'fastestLap' => [
                'awarded' => $pointSystem->fastest_lap_point_awarded,
                'type' => $pointSystem->fastest_lap_determination,
                'min_rng' => $pointSystem->fastest_lap_min_rng,
                'max_rng' => $pointSystem->fastest_lap_max_rng,
            ],
        ]);
    }

    private function eagerLoadRace(Race $race): void
    {
        $race->load([
            'stints',
            'season' => [
                'activeRacers' => [
                    'driver',
                    'entrant' => ['engine'],
                ],
                'pointSystem',
            ],
            'raceResults',
        ]);
    }
}
