<?php

namespace App\Http\Controllers;

use App\Actions\GetQualifyingResults;
use App\Contracts\HasSessions;
use App\Models\Race;
use Inertia\Inertia;
use Inertia\Response;

class ShowQualifyingPageController extends Controller
{
    public function __invoke(Race $race): Response
    {
        $this->authorize('view', $race->season->universe);

        /** @var HasSessions $format */
        $format = $race->season->qualifyingFormat;

        return Inertia::render('RaceWeekend/Qualifying', [
            'race' => $race->load('season'),
            'drivers' => (new GetQualifyingResults)->handle($race),
            'sessions' => $format->sessions(),
        ]);
    }
}
