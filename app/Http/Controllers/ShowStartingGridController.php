<?php

namespace App\Http\Controllers;

use App\Http\Resources\Race\RaceResource;
use App\Http\Resources\StartingGridResource;
use App\Models\Race;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Inertia\Inertia;
use Inertia\Response;

class ShowStartingGridController extends Controller
{
    public function __invoke(Race $race): Response|RedirectResponse|AnonymousResourceCollection
    {
        $this->authorize('view', $race->universe());

        if (! $race->qualifying_completed) {
            return to_route('weekend.qualifying', [$race]);
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

        return Inertia::render('RaceWeekend/Grid', [
            'race' => RaceResource::make($race),
            'drivers' => StartingGridResource::collection($race->qualifyingResults),
        ]);
    }
}
