<?php

namespace App\Http\Controllers;

use App\Actions\GetRaceResults;
use App\Http\Resources\Race\RaceResource;
use App\Models\Race;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ShowRacePageController extends Controller
{
    public function __invoke(Race $race, GetRaceResults $getRaceResults): Response|RedirectResponse
    {
        if (! $race->qualifying_completed) {
            return to_route('weekend.qualifying', [$race]);
        }

        $this->authorize('view', $race->universe());

        return Inertia::render('RaceWeekend/Race', [
            'race' => RaceResource::make($race->load('season', 'circuit')),
            'results' => $getRaceResults->handle($race),
        ]);
    }
}
