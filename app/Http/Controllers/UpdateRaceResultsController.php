<?php

namespace App\Http\Controllers;

use App\Actions\Races\Results\UpdateRaceDetails;
use App\Actions\Races\Results\UpdateRaceResults;
use App\Http\Requests\UpdateRaceResultsRequest;
use App\Models\Race;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class UpdateRaceResultsController extends Controller
{
    public function __invoke(
        UpdateRaceResultsRequest $request,
        Race $race,
        UpdateRaceDetails $updateRaceDetails,
        UpdateRaceResults $updateRaceResults,
    ): JsonResponse|RedirectResponse {
        $this->authorize('update', $race->universe());

        if (! $race->qualifying_completed) {
            return to_route('weekend.qualifying', $race);
        }

        DB::transaction(function () use ($request, $race, $updateRaceDetails, $updateRaceResults) {
            $updateRaceDetails->handle($race, $request->validated('current_lap'));

            $updateRaceResults->handle($request->results());
        });

        return response()->json();
    }
}
