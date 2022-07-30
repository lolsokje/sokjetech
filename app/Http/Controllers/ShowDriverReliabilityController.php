<?php

namespace App\Http\Controllers;

use App\Models\Season;
use Inertia\Inertia;
use Inertia\Response;

class ShowDriverReliabilityController extends Controller
{
    public function __invoke(Season $season): Response
    {
        $this->authorize('update', $season->universe);

        return Inertia::render('Development/Reliability/Drivers', [
            'season' => $season->append('has_active_race'),
            'drivers' => $season->activeRacers()->with(['driver', 'entrant'])->get(),
        ]);
    }
}
