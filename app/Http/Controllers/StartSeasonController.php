<?php

namespace App\Http\Controllers;

use App\Models\Season;
use Illuminate\Http\RedirectResponse;

class StartSeasonController extends Controller
{
    public function __invoke(Season $season): RedirectResponse
    {
        $this->authorize('update', $season->universe);

        if (!$season->can_start) {
            return to_route('seasons.races.index', [$season])
                ->with('error', 'Missing requirements before the season can be started');
        }

        $season->update(['started' => true]);

        return to_route('seasons.races.index', [$season])
            ->with('notice', 'Season has been started');
    }
}
