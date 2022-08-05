<?php

namespace App\Http\Controllers;

use App\Actions\Ratings\UpdateTeamRatings;
use App\Http\Requests\TeamRatingUpdateRequest;
use App\Models\Season;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UpdateTeamRatingsController extends Controller
{
    public function __invoke(TeamRatingUpdateRequest $request, Season $season): RedirectResponse
    {
        $this->authorize('update', $season->universe);
        $this->middleware(['race_in_progress']);

        (new UpdateTeamRatings($request->validated('teams')))->handle();

        return redirect(route('seasons.development.teams', [$season]))
            ->with('notice', 'Team ratings updated successfully');
    }
}
