<?php

namespace App\Http\Controllers;

use App\Http\Requests\EngineSeasonCreateRequest;
use App\Models\EngineSeason;
use App\Models\Season;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class EngineSeasonController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth'])->only('create', 'edit');
    }

    public function index(Season $season): Response
    {
        return Inertia::render('Seasons/Engines/Index', [
            'season' => $season,
            'engines' => $season->engines->load('baseEngine'),
        ]);
    }

    public function create(Season $season): Response
    {
        $this->authorize('update', $season->universe);

        return Inertia::render('Seasons/Engines/Create', [
            'season' => $season,
            'engines' => $season->baseEngines,
        ]);
    }

    public function store(EngineSeasonCreateRequest $request, Season $season): RedirectResponse
    {
        $this->authorize('update', $season->universe);

        $season->engines()->create($request->validated());

        return to_route('seasons.engines.index', $season);
    }

    public function edit(Season $season, EngineSeason $engine): Response
    {
        $this->authorize('update', $season->universe);

        return Inertia::render('Seasons/Engines/Edit', [
            'season' => $season,
            'engines' => $season->baseEngines,
            'engine' => $engine,
        ]);
    }

    public function update(EngineSeasonCreateRequest $request, Season $season, EngineSeason $engine): RedirectResponse
    {
        $this->authorize('update', $season->universe);

        $engine->update($request->validated());

        return to_route('seasons.engines.index', [$season]);
    }
}
