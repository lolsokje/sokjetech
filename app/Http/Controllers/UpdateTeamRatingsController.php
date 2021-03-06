<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamRatingUpdateRequest;
use App\Models\Entrant;
use App\Models\Season;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UpdateTeamRatingsController extends Controller
{
    public function __invoke(TeamRatingUpdateRequest $request, Season $season): RedirectResponse
    {
        $this->authorize('update', $season->universe);

        $teams = collect($request->get('teams'));

        $teams->each(function ($team) {
            Entrant::query()
                ->where('id', $team['id'])
                ->update(['rating' => $team['new']]);
        });

        return redirect(route('seasons.development.teams', [$season]))
            ->with('notice', 'Team ratings updated successfully');
    }
}
