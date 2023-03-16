<?php

namespace App\Http\Controllers;

use App\Actions\Races\Configuration\StorePointsSystemConfiguration;
use App\DataTransferObjects\Configuration\PointSystemConfiguration;
use App\Http\Requests\PointSystemConfigurationRequest;
use App\Models\Season;
use Symfony\Component\HttpFoundation\RedirectResponse;

class StorePointsConfigurationController extends Controller
{
    public function __invoke(PointSystemConfigurationRequest $request, Season $season): RedirectResponse
    {
        $this->authorize('update', $season->universe);

        (new StorePointsSystemConfiguration(
            PointSystemConfiguration::fromRequest($request),
            $season
        ))->handle();

        return to_route('seasons.configuration.points', [$season])
            ->with('notice', 'Points configuration stored');
    }
}
