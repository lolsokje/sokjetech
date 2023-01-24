<?php

namespace App\Http\Controllers\SeasonSetupCopy;

use App\Actions\Season\CopyPoints;
use App\Exceptions\InvalidSeasonRequirements;
use App\Http\Controllers\Controller;
use App\Http\Requests\CopyPointsSystemRequest;
use App\Http\Responses\InvalidSeasonRequirementsResponse;
use App\Models\Season;
use Symfony\Component\HttpFoundation\Response;

class Points extends Controller
{
    public function __invoke(CopyPointsSystemRequest $request, Season $season): Response
    {
        try {
            (new CopyPoints($request, $season))->handle();

            return response()->json([], Response::HTTP_CREATED);
        } catch (InvalidSeasonRequirements $e) {
            return (new InvalidSeasonRequirementsResponse($e))->handle();
        }
    }
}
