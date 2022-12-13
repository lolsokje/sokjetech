<?php

namespace App\Http\Controllers;

use App\Http\Resources\Development\TeamResource;
use App\Models\Season;
use Inertia\Inertia;
use Inertia\Response;

class ShowTeamDevelopmentPageController extends Controller
{
    public function __invoke(Season $season): Response
    {
        $this->authorize('update', $season->universe);

        $teams = $season->entrants()->with('team')->get();
        $teams = TeamResource::collection($teams)->toArray(request());

        return Inertia::render('Development/Teams', [
            'season' => $season->append('has_active_race'),
            'teams' => $teams,
        ]);
    }
}
