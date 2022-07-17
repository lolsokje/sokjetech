<?php

namespace App\Http\Controllers;

use App\Models\Race;
use Inertia\Inertia;
use Inertia\Response;

class ShowRaceWeekendIntroPageController extends Controller
{
    public function __invoke(Race $race): Response
    {
        return Inertia::render('RaceWeekend/Intro', [
            'race' => $race->load('season'),
        ]);
    }
}
