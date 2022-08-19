<?php

namespace App\Http\Controllers;

use App\Http\Resources\DnfReasonResource;
use App\Models\Season;
use Inertia\Inertia;

class ShowReliabilityConfigurationController extends Controller
{
    public function __invoke(Season $season)
    {
        $this->authorize('update', $season->universe);

        $season->load(['reliabilityConfiguration', 'reliabilityReasons']);

        return Inertia::render('Seasons/Configuration/Reliability', [
            'season' => $season,
            'configuration' => $season->reliabilityConfiguration,
            'reasons' => DnfReasonResource::make($season->reliabilityReasons)->toArray(request()),
        ]);
    }
}
