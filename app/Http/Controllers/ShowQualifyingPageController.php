<?php

namespace App\Http\Controllers;

use App\Http\Resources\QualifyingResultResource;
use App\Http\Resources\RaceWeekendDriverResource;
use App\Models\Race;
use Inertia\Inertia;
use Inertia\Response;

class ShowQualifyingPageController extends Controller
{
    public function __invoke(Race $race): Response
    {
        $this->authorize('view', $race->season->universe);

        $race->load([
            'season.activeRacers.driver',
            'season.activeRacers.entrant.engine',
            'season.qualifyingFormat',
            'qualifyingResults',
        ]);

        $drivers = RaceWeekendDriverResource::collection($race->season->activeRacers);
        return Inertia::render('RaceWeekend/Qualifying', [
            'race' => $race,
            'drivers' => $drivers->toArray(request()),
            'qualifyingResults' => QualifyingResultResource::collection($race->qualifyingResults)->toArray(request()),
        ]);
    }
}
