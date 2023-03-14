<?php

namespace App\Http\Controllers\SeasonSetupCopy;

use App\Actions\Season\CopyTeams;
use App\Exceptions\InvalidSeasonRequirements;
use App\Http\Controllers\Controller;
use App\Http\Requests\CopyTeamSetupRequest;
use App\Http\Responses\InvalidSeasonRequirementsResponse;
use App\Models\Season;
use Symfony\Component\HttpFoundation\Response;

class Teams extends Controller
{
    public function __invoke(CopyTeamSetupRequest $request, Season $season): Response
    {
        try {
            (new CopyTeams($request, $season))->handle();

            return response()->json(null, Response::HTTP_CREATED);
        } catch (InvalidSeasonRequirements $e) {
            return (new InvalidSeasonRequirementsResponse($e))->handle();
        }
    }
}
