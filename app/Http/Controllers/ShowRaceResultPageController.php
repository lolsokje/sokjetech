<?php

namespace App\Http\Controllers;

use App\Http\Resources\RaceResultPageResource;
use App\Http\Resources\RaceWeekend\RaceResultResource;
use App\Models\Race;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ShowRaceResultPageController extends Controller
{
    public function __invoke(Race $race): Response|RedirectResponse
    {
        if (! $race->completed) {
            return to_route('weekend.race', [$race]);
        }

        $this->authorize('view', $race->universe());

        $race->load([
            'season' => ['pointSystem'],
            'raceResults' => [
                'racer' => [
                    'driver',
                    'entrant' => ['engine'],
                ],
            ],
        ]);

        return Inertia::render('RaceWeekend/Results', [
            'race' => RaceResultResource::array($race),
            'drivers' => RaceResultPageResource::collection($race->raceResults)->toArray(request()),
        ]);
    }
}
