<?php

namespace App\Http\Controllers\SeasonSetupCopy;

use App\Actions\Season\Copy\CopyDrivers;
use App\Exceptions\InvalidSeasonRequirements;
use App\Http\Requests\CopyLineupComponentRequest;
use App\Http\Responses\InvalidSeasonRequirementsResponse;
use App\Models\Season;
use Symfony\Component\HttpFoundation\Response;

class Drivers
{
    public function __invoke(CopyLineupComponentRequest $request, Season $season)
    {
        try {
            (new CopyDrivers($request->getSourceSeason(), $season))->handle(
                copyRatings: $request->copyRatings(),
            );

            return response()->json(null, Response::HTTP_CREATED);
        } catch (InvalidSeasonRequirements $e) {
            return (new InvalidSeasonRequirementsResponse($e))->handle();
        }
    }
}
