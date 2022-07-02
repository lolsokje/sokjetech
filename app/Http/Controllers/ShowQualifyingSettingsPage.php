<?php

namespace App\Http\Controllers;

use App\Enums\QualifyingFormat;
use App\Models\Season;
use Inertia\Inertia;

class ShowQualifyingSettingsPage extends Controller
{
    public function __invoke(Season $season)
    {
        return Inertia::render('Seasons/Configuration/Qualifying', [
            'season' => $season,
            'formats' => QualifyingFormat::cases(),
        ]);
    }
}
