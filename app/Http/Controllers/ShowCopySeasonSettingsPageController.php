<?php

namespace App\Http\Controllers;

use App\Models\Season;
use Inertia\Inertia;

class ShowCopySeasonSettingsPageController extends Controller
{
    public function __invoke(Season $season)
    {
        $this->authorize('update', $season->universe);
        
        if ($season->started || $season->completed) {
            return to_route('seasons.races.index', [$season])
                ->with('error', "You can't copy settings to an already started or completed season");
        }

        return Inertia::render('Seasons/CopySetup', [
            'season' => $season,
            'seasons' => $season->series->seasons,
        ]);
    }
}
