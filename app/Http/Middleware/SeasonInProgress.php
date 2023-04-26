<?php

namespace App\Http\Middleware;

use App\Models\Season;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SeasonInProgress
{
    public function handle(Request $request, Closure $next): RedirectResponse|JsonResponse|Response
    {
        /** @var Season $season */
        $season = $request->route('race')->season;

        if ($season->started) {
            return $next($request);
        }

        return to_route('seasons.races.index', [$season])
            ->with('error', 'Season not started yet');
    }
}
