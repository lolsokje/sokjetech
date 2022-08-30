<?php

namespace App\Http\Controllers\SeasonSetupCopy;

use App\Actions\Season\CopyRaces;
use App\Exceptions\InvalidSeasonRequirements;
use App\Http\Controllers\Controller;
use App\Http\Requests\CopyRaceSetupRequest;
use App\Http\Responses\InvalidSeasonRequirementsResponse;
use App\Models\Season;
use Symfony\Component\HttpFoundation\Response;

class Races extends Controller
{
    public function __invoke(CopyRaceSetupRequest $request, Season $season): Response
    {
        try {
            (new CopyRaces($request, $season))->handle();

            return response()->json([], Response::HTTP_CREATED);
        } catch (InvalidSeasonRequirements $e) {
            return (new InvalidSeasonRequirementsResponse($e))->handle();
        }
    }
}
