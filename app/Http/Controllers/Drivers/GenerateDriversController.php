<?php

namespace App\Http\Controllers\Drivers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Drivers\GenerateDriversRequest;
use App\Models\Universe;
use App\Support\DriverGenerator\Generator;

class GenerateDriversController extends Controller
{
    public function __invoke(GenerateDriversRequest $request, Universe $universe): array
    {
        $generator = new Generator($request->language(), $request->gender());

        return $generator->generate($request->start(), $request->end(), $request->amount());
    }
}
