<?php

namespace App\Http\Controllers;

use App\Actions\Races\CalculatePointsScored;
use App\Actions\Races\CompleteRace;
use App\Jobs\CalculateDriverChampionshipStandingsJob;
use App\Jobs\CalculateTeamChampionshipStandingsJob;
use App\Models\Race;
use Illuminate\Http\RedirectResponse;

class CompleteRaceController extends Controller
{
    public function __invoke(Race $race): RedirectResponse
    {
        $this->authorize('update', $race->universe());

        (new CompleteRace($race))->handle();
        (new CalculatePointsScored($race))->handle();

        CalculateDriverChampionshipStandingsJob::dispatch($race->season);
        CalculateTeamChampionshipStandingsJob::dispatch($race->season);

        return to_route('weekend.results', [$race]);
    }
}
