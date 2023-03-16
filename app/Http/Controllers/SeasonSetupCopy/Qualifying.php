<?php

namespace App\Http\Controllers\SeasonSetupCopy;

use App\Actions\Season\Copy\CopyQualifyingFormat;
use App\Exceptions\InvalidSeasonRequirements;
use App\Http\Controllers\Controller;
use App\Http\Requests\CopyQualifyingFormatRequest;
use App\Http\Responses\InvalidSeasonRequirementsResponse;
use App\Models\Season;
use Symfony\Component\HttpFoundation\Response;

class Qualifying extends Controller
{
    public function __invoke(CopyQualifyingFormatRequest $request, Season $season): Response
    {
        try {
            (new CopyQualifyingFormat($request->getSourceSeason(), $season))->handle();

            return response()->json([], Response::HTTP_CREATED);
        } catch (InvalidSeasonRequirements $e) {
            return (new InvalidSeasonRequirementsResponse($e))->handle();
        }
    }
}
