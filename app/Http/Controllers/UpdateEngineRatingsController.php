<?php

namespace App\Http\Controllers;

use App\Actions\Ratings\UpdateEngineRatings;
use App\Http\Requests\EngineRatingUpdateRequest;
use App\Models\Season;

class UpdateEngineRatingsController extends Controller
{
    public function __invoke(EngineRatingUpdateRequest $request, Season $season)
    {
        $this->authorize('update', $season->universe);
        $this->middleware(['race_in_progress']);

        (new UpdateEngineRatings($request->validated('engines')))->handle();

        return redirect(route('seasons.development.engines', [$season]))
            ->with('notice', 'Engine ratings updated successfully');
    }
}
