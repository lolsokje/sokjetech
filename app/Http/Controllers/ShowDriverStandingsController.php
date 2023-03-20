<?php

namespace App\Http\Controllers;

use App\Actions\Season\Standings\Driver\GenerateStandingsAction;
use App\Http\Resources\SeasonStandingResource;
use App\Models\Season;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Inertia\Inertia;
use Inertia\Response;

class ShowDriverStandingsController extends Controller
{
    public function __invoke(Season $season): Response
    {
        $this->authorize('view', $season->universe);

        $season->load([
            'driverChampionshipStandings' => fn (HasMany $query) => $query->orderBy('position')->with('driver'),
            'driversWithParticipation' => ['entrant'],
            'pointSystem',
            'raceResults' => ['racer', 'race'],
            'races' => fn (HasMany $query) => $query->orderBy('order')->with('circuit'),
        ]);

        return Inertia::render('Standings/Drivers', [
            'season' => SeasonStandingResource::make($season)->toArray(request()),
            'races' => $season->races,
            'standings' => (new GenerateStandingsAction($season))->handle(),
        ]);
    }
}
