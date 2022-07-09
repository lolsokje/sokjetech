<?php

namespace App\Http\Controllers;

use App\Models\Season;
use Inertia\Inertia;
use Inertia\Response;

class ShowPointsConfigurationController extends Controller
{
    public function __invoke(Season $season): Response
    {
        $this->authorize('update', $season->universe);

        return Inertia::render('Seasons/Configuration/Points', [
            'season' => $season->load(['pointSystem']),
            'points' => $season->points() ?? [],
        ]);
    }
}
