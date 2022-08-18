<?php

namespace App\Http\Controllers\SeasonSetupCopy;

use App\Actions\Season\CopyQualifyingFormat;
use App\Http\Controllers\Controller;
use App\Http\Requests\CopyQualifyingFormatRequest;
use App\Models\Season;
use Symfony\Component\HttpFoundation\Response;

class Qualifying extends Controller
{
    public function __invoke(CopyQualifyingFormatRequest $request, Season $season): Response
    {
        (new CopyQualifyingFormat($request, $season))->handle();

        return response()->json([], Response::HTTP_CREATED);
    }
}
