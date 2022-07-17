<?php

namespace App\Http\Controllers;

use App\Models\Race;
use Illuminate\Http\RedirectResponse;

class CompleteQualifyingController extends Controller
{
    public function __invoke(Race $race): RedirectResponse
    {
        $this->authorize('update', $race->season->universe);

        $race->update(['qualifying_completed' => true]);

        return to_route('weekend.grid', $race)
            ->with('notice', 'Qualifying completed');
    }
}
