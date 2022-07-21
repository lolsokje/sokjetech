<?php

namespace App\Http\Controllers;

use App\Actions\Races\StoreRaceResultsAction;
use App\Http\Requests\StoreRaceResultsRequest;
use App\Models\Race;
use Illuminate\Http\JsonResponse;

class StoreRaceResultsController extends Controller
{
    public function __invoke(StoreRaceResultsRequest $request, Race $race): JsonResponse
    {
        $this->authorize('update', $race->universe());

        (new StoreRaceResultsAction($request, $race))->handle();

        return response()->json([]);
    }
}
