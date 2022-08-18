<?php

namespace App\Http\Controllers;

use App\Models\Season;
use Inertia\Inertia;

class ShowCopySeasonSettingsPageController extends Controller
{
    public function __invoke(Season $season)
    {
        $this->authorize('update', $season->universe);

        return Inertia::render('Seasons/CopySetup', [
            'season' => $season,
            'seasons' => $season->series->seasons,
        ]);
    }
}
