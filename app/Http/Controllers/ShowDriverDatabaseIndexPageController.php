<?php

namespace App\Http\Controllers;

use App\Actions\GetSharedDrivers;
use App\Http\Requests\DriverFilterRequest;
use App\Http\Resources\DriverResource;
use Inertia\Inertia;
use Inertia\Response;

class ShowDriverDatabaseIndexPageController extends Controller
{
    public function __invoke(DriverFilterRequest $request): Response
    {
        $drivers = (new GetSharedDrivers($request))->handle();

        return Inertia::render('Database/Drivers/Index', [
            'drivers' => DriverResource::collection($drivers)->toArray($request),
            'links' => $drivers->linkCollection(),
            'universes' => auth()->user()->universes()->orderBy('name')->get(),
            'filters' => $request->validated(),
        ]);
    }
}
