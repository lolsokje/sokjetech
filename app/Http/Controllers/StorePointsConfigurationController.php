<?php

namespace App\Http\Controllers;

use App\Actions\Races\Configuration\StorePointsSystem;
use App\Http\Requests\PointSystemConfigurationRequest;
use App\Models\Season;
use Symfony\Component\HttpFoundation\RedirectResponse;

class StorePointsConfigurationController extends Controller
{
    public function __invoke(PointSystemConfigurationRequest $request, Season $season): RedirectResponse
    {
        $this->middleware(['season_started']);
        $this->authorize('update', $season->universe);

        (new StorePointsSystem($request, $season))->handle();

        return to_route('seasons.configuration.points', [$season])
            ->with('notice', 'Points configuration stored');
    }
}
