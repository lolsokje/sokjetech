<?php

namespace App\Http\Controllers;

use App\Actions\Races\CalculateIntroPageStandingsAction;
use App\Http\Resources\GeneralRaceResource;
use App\Models\Race;
use Inertia\Inertia;
use Inertia\Response;

class ShowRaceWeekendIntroPageController extends Controller
{
    public function __invoke(Race $race): Response
    {
        [$driverStandings, $teamStandings] = (new CalculateIntroPageStandingsAction($race))->handle();

        return Inertia::render('RaceWeekend/Intro', [
            'race' => GeneralRaceResource::array($race),
            'teamStandings' => $teamStandings,
            'driverStandings' => $driverStandings,
        ]);
    }
}
