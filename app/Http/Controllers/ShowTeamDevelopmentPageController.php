<?php

namespace App\Http\Controllers;

use App\Models\Season;
use Inertia\Inertia;
use Inertia\Response;

class ShowTeamDevelopmentPageController extends Controller
{
    public function __invoke(Season $season): Response
    {
        $this->authorize('update', $season->universe);

        return Inertia::render('Development/Teams', [
            'season' => $season->append('has_active_race'),
            'teams' => $season->entrants()->with('team')->get(),
        ]);
    }
}
