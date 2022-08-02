<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DisallowModifyingStartedSeason
{
    public function handle(Request $request, Closure $next): RedirectResponse|Response|JsonResponse
    {
        $season = $request->route('season');

        if ($season->started) {
            return back()
                ->with('error', 'The season has started and can therefore no longer be modified');
        }
        return $next($request);
    }
}
