<?php

namespace App\Http\Controllers\SeasonSetupCopy;

use App\Actions\Season\CopyPoints;
use App\Http\Controllers\Controller;
use App\Http\Requests\CopyPointsSystemRequest;
use App\Models\Season;
use Symfony\Component\HttpFoundation\Response;

class Points extends Controller
{
    public function __invoke(CopyPointsSystemRequest $request, Season $season): Response
    {
        (new CopyPoints($request, $season))->handle();

        return response()->json([], Response::HTTP_CREATED);
    }
}
