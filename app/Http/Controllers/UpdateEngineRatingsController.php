<?php

namespace App\Http\Controllers;

use App\Http\Requests\EngineRatingUpdateRequest;
use App\Models\EngineSeason;
use App\Models\Season;

class UpdateEngineRatingsController extends Controller
{
    public function __invoke(EngineRatingUpdateRequest $request, Season $season)
    {
        $this->authorize('update', $season->universe);

        $engines = collect($request->get('engines'));

        $engines->each(function ($engine) {
            EngineSeason::query()
                ->where('id', $engine['id'])
                ->update(['rating' => $engine['new']]);
        });

        return redirect(route('seasons.development.engines', [$season]))
            ->with('notice', 'Engine ratings updated successfully');
    }
}
