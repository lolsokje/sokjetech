<?php

namespace App\Http\Controllers;

use App\Models\Season;
use Inertia\Inertia;
use Inertia\Response;

class ShowEngineDevelopmentPageController extends Controller
{
    public function __invoke(Season $season): Response
    {
        $this->authorize('update', $season->universe);

        return Inertia::render('Development/Engines', [
            'season' => $season,
            'engines' => $season->engines,
        ]);
    }
}
