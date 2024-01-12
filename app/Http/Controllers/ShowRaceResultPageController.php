<?php

namespace App\Http\Controllers;

use App\Actions\GetRaceResults;
use App\Http\Resources\RaceWeekend\RaceResultResource;
use App\Models\Race;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ShowRaceResultPageController extends Controller
{
    public function __invoke(Race $race, GetRaceResults $getRaceResults): Response|RedirectResponse
    {
        if (! $race->completed) {
            return to_route('weekend.race', [$race]);
        }

        $this->authorize('view', $race->universe());

        return Inertia::render('RaceWeekend/Results', [
            'race' => RaceResultResource::array($race),
            'results' => $getRaceResults->handle($race),
        ]);
    }
}
