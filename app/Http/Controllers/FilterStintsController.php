<?php

namespace App\Http\Controllers;

use App\Actions\GetStints;
use App\Http\Requests\StintFilterRequest;
use Symfony\Component\HttpFoundation\Response;

class FilterStintsController extends Controller
{
    public function __invoke(StintFilterRequest $request): Response
    {
        return response()->json((new GetStints($request))->handle());
    }
}
