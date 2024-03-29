<?php

namespace App\Http\Controllers;

use App\Actions\GetRaceResults;
use App\Http\Resources\DnfReasonResource;
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

        $pointSystem = $race->season->pointSystem;
        return Inertia::render('RaceWeekend/Race', [
            'race' => $race->load('season'),
            'drivers' => (new GetRaceResults())->handle($race),
            'fastestLap' => [
                'awarded' => $pointSystem->fastest_lap_point_awarded,
                'type' => $pointSystem->fastest_lap_determination,
                'min_rng' => $pointSystem->fastest_lap_min_rng,
                'max_rng' => $pointSystem->fastest_lap_max_rng,
            ],
            'reliability_configuration' => $race->season->reliabilityConfiguration,
            'reliability_reasons' => DnfReasonResource::make($race->season->reliabilityReasons)->toArray(request()),
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
                'reliabilityConfiguration',
                'reliabilityReasons',
            ],
            'raceResults',
        ]);
    }
}
