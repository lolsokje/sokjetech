<?php

namespace App\Http\Controllers\SeasonSetupCopy;

use App\Actions\Season\CopyLineups;
use App\Http\Controllers\Controller;
use App\Http\Requests\CopyTeamSetupRequest;
use App\Models\Season;
use Symfony\Component\HttpFoundation\Response;

class Teams extends Controller
{
    public function __invoke(CopyTeamSetupRequest $request, Season $season): Response
    {
        (new CopyLineups($request, $season))->handle();

        return response()->json(null, Response::HTTP_CREATED);
    }
}
