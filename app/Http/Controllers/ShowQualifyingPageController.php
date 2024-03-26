<?php

namespace App\Http\Controllers;

use App\Actions\GetQualifyingResults;
use App\Contracts\HasSessions;
use App\Http\Resources\Race\RaceResource;
use App\Models\Race;
use Inertia\Inertia;
use Inertia\Response;

class ShowQualifyingPageController extends Controller
{
    public function __invoke(
        Race $race,
        GetQualifyingResults $getQualifyingResults,
    ): Response {
        $this->authorize('view', $race->season->universe);

        /** @var HasSessions $format */
        $format = $race->season->qualifyingFormat;

        return Inertia::render('RaceWeekend/Qualifying', [
            'race' => RaceResource::make($race->load('season.qualifyingFormat')),
            'results' => $getQualifyingResults->handle($race),
            'sessions' => $format->sessions(),
        ]);
    }
}
