<?php

namespace App\Http\Controllers\SeasonSetupCopy;

use App\Actions\Season\CopyReliabilityConfiguration;
use App\Http\Controllers\Controller;
use App\Http\Requests\CopyReliabilityConfigurationRequest;
use App\Models\Season;
use Symfony\Component\HttpFoundation\Response;

class Reliability extends Controller
{
    public function __invoke(CopyReliabilityConfigurationRequest $request, Season $season): Response
    {
        (new CopyReliabilityConfiguration($request, $season))->handle();

        return response()->json([], Response::HTTP_CREATED);
    }
}