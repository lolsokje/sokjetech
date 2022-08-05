<?php

namespace App\Http\Controllers;

use App\Models\Season;
use Inertia\Inertia;
use Inertia\Response;

class ShowEngineReliabilityController extends Controller
{
    public function __invoke(Season $season): Response
    {
        $this->authorize('update', $season->universe);

        return Inertia::render('Development/Reliability/Engines', [
            'season' => $season->append('has_active_race'),
            'engines' => $season->engines,
        ]);
    }
}
