<?php

namespace App\Http\Controllers;

use App\Actions\Races\Results\GetQualifyingResults;
use App\Http\Resources\Race\RaceResource;
use App\Http\Resources\StartingGridResource;
use App\Models\Race;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ShowStartingGridController extends Controller
{
    public function __invoke(
        Race $race,
        GetQualifyingResults $getQualifyingResults,
    ): Response|RedirectResponse {
        $this->authorize('view', $race->universe());

        if (! $race->qualifying_completed) {
            return to_route('weekend.qualifying', [$race]);
        }

        return Inertia::render('RaceWeekend/Grid', [
            'race' => RaceResource::make($race),
            'drivers' => StartingGridResource::collection($getQualifyingResults->handle($race->id)),
        ]);
    }
}
