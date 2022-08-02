<?php

namespace App\Http\Controllers;

use App\Actions\Races\StoreQualifyingResultsAction;
use App\Http\Requests\StoreQualifyingResultsRequest;
use App\Models\Race;
use Illuminate\Http\JsonResponse;

class StoreQualifyingResultsController extends Controller
{
    public function __invoke(Race $race, StoreQualifyingResultsRequest $request): JsonResponse
    {
        $this->authorize('update', $race->season->universe);

        (new StoreQualifyingResultsAction($request->details(), $request->drivers(), $race))->handle();

        return response()->json([]);
    }
}
