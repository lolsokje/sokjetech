<?php

namespace App\Http\Controllers;

use App\Models\Season;
use Inertia\Inertia;
use Inertia\Response;

class ShowDriverDevelopmentPageController extends Controller
{
    public function __invoke(Season $season): Response
    {
        $this->authorize('update', $season->universe);

        return Inertia::render('Development/Drivers', [
            'season' => $season,
            'drivers' => $season->activeRacers->load(['driver', 'entrant']),
        ]);
    }
}
