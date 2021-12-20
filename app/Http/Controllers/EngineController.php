<?php

namespace App\Http\Controllers;

use App\Http\Requests\EngineCreateRequest;
use App\Models\Engine;
use App\Models\Series;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class EngineController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth'])->only('create', 'edit');
    }

    public function index(Series $series): Response
    {
        return Inertia::render('Engines/Index', [
            'series' => $series->load(['engines' => fn(HasMany $query) => $query->orderBy('name')]),
            'can' => [
                'edit' => request()->user()?->can('update', $series->universe),
            ],
        ]);
    }

    public function create(Series $series): Response
    {
        $this->authorize('update', $series->universe);

        return Inertia::render('Engines/Create', [
            'series' => $series,
        ]);
    }

    public function store(EngineCreateRequest $request, Series $series): RedirectResponse
    {
        $this->authorize('update', $series->universe);

        $series->engines()->create($request->validated());

        return redirect(route('series.engines.index', [$series]))
            ->with('notice', 'Engine created');
    }

    public function edit(Series $series, Engine $engine): Response
    {
        $this->authorize('update', $series->universe);

        return Inertia::render('Engines/Edit', [
            'series' => $series,
            'engine' => $engine,
        ]);
    }

    public function update(EngineCreateRequest $request, Series $series, Engine $engine): RedirectResponse
    {
        $this->authorize('update', $series->universe);

        $engine->update($request->validated());

        return redirect(route('series.engines.index', [$series]))
            ->with('notice', 'Engine updated');
    }
}
