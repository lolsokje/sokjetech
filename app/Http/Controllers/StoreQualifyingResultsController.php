<?php

namespace App\Http\Controllers;

use App\Actions\Races\Results\StoreQualifyingResultsAction;
use App\Actions\Races\Results\UpdateQualifyingDetails;
use App\Http\Requests\StoreQualifyingResultsRequest;
use App\Models\Race;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StoreQualifyingResultsController extends Controller
{
    public function __invoke(
        Race $race,
        StoreQualifyingResultsRequest $request,
        UpdateQualifyingDetails $updateQualifyingDetails,
        StoreQualifyingResultsAction $storeQualifyingResults,
    ): JsonResponse {
        $this->authorize('update', $race->season->universe);

        DB::transaction(function () use ($race, $request, $updateQualifyingDetails, $storeQualifyingResults) {
            $updateQualifyingDetails->handle($race, $request->details());

            $storeQualifyingResults->handle(
                $request->drivers(),
                $race->id,
            );
        });

        return response()->json();
    }
}
