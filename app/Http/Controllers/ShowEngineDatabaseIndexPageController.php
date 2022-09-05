<?php

namespace App\Http\Controllers;

use App\Actions\GetSharedEngines;
use App\Http\Requests\EngineFilterRequest;
use App\Http\Resources\EngineResource;
use Inertia\Inertia;

class ShowEngineDatabaseIndexPageController extends Controller
{
    public function __invoke(EngineFilterRequest $request)
    {
        $engines = (new GetSharedEngines($request))->handle();

        return Inertia::render('Database/Engines/Index', [
            'engines' => EngineResource::collection($engines)->toArray($request),
            'links' => $engines->linkCollection(),
            'series' => auth()->user()->series()->orderBy('name')->with('universe:id,name')->get(),
            'filters' => [],
        ]);
    }
}
