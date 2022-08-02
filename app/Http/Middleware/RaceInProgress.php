<?php

namespace App\Http\Middleware;

use App\Models\Season;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RaceInProgress
{
    public function handle(Request $request, Closure $next): RedirectResponse|JsonResponse|Response
    {
        /** @var Season $season */
        $season = $request->route('season');

        if (!$season->hasActiveRace) {
            return $next($request);
        }

        return to_route('seasons.races.index', [$season])
            ->with(
                'error',
                "There's currently a race in progress, editing and development disabled until the race is done",
            );
    }
}
