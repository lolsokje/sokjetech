<?php

namespace App\Http\Controllers;

use App\Models\Season;
use Illuminate\Http\RedirectResponse;

class StartSeasonController extends Controller
{
    public function __invoke(Season $season): RedirectResponse
    {
        $season->update(['started' => true]);

        return redirect(route('seasons.races.index', [$season]))
            ->with('notice', 'Season has been started');
    }
}
