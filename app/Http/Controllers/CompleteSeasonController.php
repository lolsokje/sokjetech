<?php

namespace App\Http\Controllers;

use App\Models\Season;
use Illuminate\Http\RedirectResponse;

class CompleteSeasonController extends Controller
{
    public function __invoke(Season $season): RedirectResponse
    {
        $this->authorize('update', $season->universe);

        if (!$season->can_complete) {
            return to_route('seasons.races.index', [$season])
                ->with('error', "Can't mark the current season as completed");
        }

        $season->update(['completed' => true]);

        return to_route('seasons.races.index', [$season])
            ->with('notice', 'Marked the current season as completed');
    }
}
