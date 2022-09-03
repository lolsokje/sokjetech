<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamCopyRequest;
use App\Models\Team;
use App\Models\Universe;

class CopyTeamController extends Controller
{
    public function __invoke(TeamCopyRequest $request, Team $team)
    {
        $universe = Universe::query()->find($request->validated('universe_id'));
        $this->authorize('update', $universe);

        $newTeam = $team->replicate();
        $newTeam->universe()->associate($universe);
        $newTeam->save();

        return to_route('database.teams.index')
            ->with('notice', 'Team copied');
    }
}
