<?php

namespace App\Http\Controllers;

use App\Actions\Races\Configuration\StoreReliabilityConfiguration;
use App\DataTransferObjects\Configuration\ReliabilityConfiguration;
use App\Http\Requests\StoreReliabilityConfigurationRequest;
use App\Models\Season;

class StoreReliabilityConfigurationController extends Controller
{
    public function __invoke(StoreReliabilityConfigurationRequest $request, Season $season)
    {
        (new StoreReliabilityConfiguration(
            new ReliabilityConfiguration(
                $request->validated('min_rng'),
                $request->validated('max_rng'),
                $request->validated('reasons')
            ),
            $season
        ))->handle();

        return to_route('seasons.configuration.reliability', [$season])
            ->with('notice', 'Reliability configuration saved');
    }
}
