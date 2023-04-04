<?php

namespace App\Http\Controllers\Drivers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Drivers\GenerateDriversRequest;
use App\Models\Universe;
use LilPecky\RandomPersonGenerator\Factory;

class GenerateDriversController extends Controller
{
    public function __invoke(GenerateDriversRequest $request, Universe $universe): array
    {
        $generator = Factory::createWithRandomLocale($request->language());

        return $generator->persons(
            amount: $request->amount(),
            startDate: $request->start(),
            endDate: $request->end(),
            gender: $request->gender(),
        );
    }
}
