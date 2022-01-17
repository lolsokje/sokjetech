<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamReliabilityUpdateRequest;
use App\Models\Entrant;
use App\Models\Season;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UpdateTeamReliabilityController extends Controller
{
    public function __invoke(TeamReliabilityUpdateRequest $request, Season $season): RedirectResponse
    {
        $this->authorize('update', $season->universe);

        $teams = collect($request->get('teams'));

        $teams->each(function ($team) {
            Entrant::query()
                ->where('id', $team['id'])
                ->update(['reliability' => $team['new']]);
        });

        return redirect(route('seasons.development.reliability.teams', [$season]))
            ->with('notice', 'Team reliabilities updated successfully');
    }
}
