<?php

namespace App\Http\Controllers;

use App\Actions\GetSharedCircuits;
use App\Http\Requests\CircuitFilterRequest;
use App\Http\Resources\CircuitResource;
use Inertia\Inertia;

class ShowCircuitDatabaseIndexPageController extends Controller
{
    public function __invoke(CircuitFilterRequest $request)
    {
        $circuits = (new GetSharedCircuits($request))->handle();

        return Inertia::render('Database/Circuits/Index', [
            'links' => $circuits->linkCollection(),
            'circuits' => CircuitResource::collection($circuits),
            'filters' => $request->validated(),
        ]);
    }
}
