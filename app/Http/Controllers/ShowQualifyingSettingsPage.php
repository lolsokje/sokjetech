<?php

namespace App\Http\Controllers;

use App\Enums\QualifyingFormat;
use App\Models\Season;
use Inertia\Inertia;

class ShowQualifyingSettingsPage extends Controller
{
    public function __invoke(Season $season)
    {
        $this->authorize('update', $season->universe);

        return Inertia::render('Seasons/Configuration/Qualifying', [
            'season' => $season->load('qualifyingFormat'),
            'formats' => QualifyingFormat::labels(),
        ]);
    }
}
