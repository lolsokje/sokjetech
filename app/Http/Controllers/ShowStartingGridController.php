<?php

namespace App\Http\Controllers;

use App\Http\Resources\StartingGridResource;
use App\Models\Race;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ShowStartingGridController extends Controller
{
    public function __invoke(Race $race): Response|RedirectResponse
    {
        if (!$race->qualifying_completed) {
            return to_route('weekend.qualifying', [$race]);
        }

        $this->authorize('view', $race->universe());

        if (!$race->qualifying_completed) {
            return to_route('weekend.intro', [$race]);
        }

        $race->load([
            'season',
            'qualifyingResults' => [
                'racer' => [
                    'driver',
                    'entrant',
                ],
            ],
        ]);

        $drivers = StartingGridResource::collection($race->qualifyingResults);

        return Inertia::render('RaceWeekend/Grid', [
            'race' => $race,
            'drivers' => $drivers->toArray(request()),
        ]);
    }
}
