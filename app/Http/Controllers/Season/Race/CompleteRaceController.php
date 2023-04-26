<?php

namespace App\Http\Controllers\Season\Race;

use App\Actions\Races\CalculatePointsScored;
use App\Actions\Races\CompleteRace;
use App\Actions\Season\Standings\CalculateDriverChampionshipStandingsAction;
use App\Actions\Season\Standings\CalculateTeamChampionshipStandingsAction;
use App\Http\Controllers\Controller;
use App\Jobs\CalculateChampionshipStandingsJob;
use App\Models\Race;
use Illuminate\Http\RedirectResponse;

class CompleteRaceController extends Controller
{
    public function __invoke(Race $race): RedirectResponse
    {
        $this->authorize('update', $race->universe());

        (new CompleteRace($race))->handle();
        (new CalculatePointsScored($race))->handle();

        CalculateChampionshipStandingsJob::dispatch(
            new CalculateDriverChampionshipStandingsAction($race->season),
        );
        CalculateChampionshipStandingsJob::dispatch(
            new CalculateTeamChampionshipStandingsAction($race->season),
        );

        return to_route('weekend.results', [$race]);
    }
}
