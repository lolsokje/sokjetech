<?php

namespace App\Http\Controllers;

use App\Actions\Races\CreateRaceResultsAction;
use App\Models\Race;
use Illuminate\Http\RedirectResponse;

class CompleteQualifyingController extends Controller
{
    public function __invoke(Race $race): RedirectResponse
    {
        $this->authorize('update', $race->season->universe);

        $race->update(['qualifying_completed' => true]);

        (new CreateRaceResultsAction($race))->handle();

        return to_route('weekend.grid', $race)
            ->with('notice', 'Qualifying completed');
    }
}
