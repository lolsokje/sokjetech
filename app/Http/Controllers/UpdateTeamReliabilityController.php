<?php

namespace App\Http\Controllers;

use App\Actions\Ratings\UpdateTeamRatings;
use App\Http\Requests\TeamReliabilityUpdateRequest;
use App\Models\Season;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UpdateTeamReliabilityController extends Controller
{
    public function __invoke(TeamReliabilityUpdateRequest $request, Season $season): RedirectResponse
    {
        $this->authorize('update', $season->universe);
        $this->middleware(['race_in_progress']);

        (new UpdateTeamRatings($request->validated('teams'), 'reliability'))->handle();

        return redirect(route('seasons.development.reliability.teams', [$season]))
            ->with('notice', 'Team reliabilities updated successfully');
    }
}
