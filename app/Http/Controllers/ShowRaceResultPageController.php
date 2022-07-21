<?php

namespace App\Http\Controllers;

use App\Http\Resources\RaceResultPageResource;
use App\Models\Race;
use Inertia\Inertia;
use Inertia\Response;

class ShowRaceResultPageController extends Controller
{
    public function __invoke(Race $race): Response
    {
        $this->authorize('view', $race->universe());

        $race->load([
            'raceResults' => [
                'racer' => [
                    'driver',
                    'entrant' => ['engine'],
                ],
            ],
        ]);

        return Inertia::render('RaceWeekend/Results', [
            'race' => $race,
            'drivers' => RaceResultPageResource::collection($race->raceResults)->toArray(request()),
        ]);
    }
}
