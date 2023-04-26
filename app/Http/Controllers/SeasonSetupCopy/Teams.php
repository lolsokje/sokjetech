<?php

namespace App\Http\Controllers\SeasonSetupCopy;

use App\Actions\Season\Copy\CopyTeamsAndEngines;
use App\Exceptions\InvalidSeasonRequirements;
use App\Http\Controllers\Controller;
use App\Http\Requests\CopyLineupComponentRequest;
use App\Http\Responses\InvalidSeasonRequirementsResponse;
use App\Models\Season;
use Symfony\Component\HttpFoundation\Response;

class Teams extends Controller
{
    public function __invoke(CopyLineupComponentRequest $request, Season $season): Response
    {
        try {
            (new CopyTeamsAndEngines($request->getSourceSeason(), $season))->handle(
                copyRatings: $request->copyRatings(),
            );

            return response()->json(null, Response::HTTP_CREATED);
        } catch (InvalidSeasonRequirements $e) {
            return (new InvalidSeasonRequirementsResponse($e))->handle();
        }
    }
}
