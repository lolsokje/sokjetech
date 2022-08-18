<?php

namespace App\Http\Controllers;

use App\Actions\Races\Configuration\StoreReliabilityConfiguration;
use App\Http\Requests\StoreReliabilityConfigurationRequest;
use App\Models\Season;

class StoreReliabilityConfigurationController extends Controller
{
    public function __invoke(StoreReliabilityConfigurationRequest $request, Season $season)
    {
        (new StoreReliabilityConfiguration($request, $season))->handle();

        return to_route('seasons.configuration.reliability', [$season]);
    }
}
