<?php

namespace App\Http\Controllers;

use App\Actions\Ratings\UpdateEngineRatings;
use App\Http\Requests\EngineReliabilityUpdateRequest;
use App\Models\Season;

class UpdateEngineReliabilityController extends Controller
{
    public function __invoke(EngineReliabilityUpdateRequest $request, Season $season)
    {
        $this->authorize('update', $season->universe);
        $this->middleware(['race_in_progress']);

        (new UpdateEngineRatings($request->validated('engines'), 'reliability'))->handle();

        return redirect(route('seasons.development.reliability.engines', [$season]))
            ->with('notice', 'Engine reliabilities updated successfully');
    }
}
