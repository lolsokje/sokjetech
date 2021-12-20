<?php

namespace App\Http\Controllers;

use App\Http\Requests\SeriesCreateRequest;
use App\Models\Series;
use App\Models\Universe;
use Gate;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SeriesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth'])->only('create', 'edit');
    }

    public function index(Universe $universe): Response
    {
        return Inertia::render('Series/Index', [
            'universe' => $universe->load(['series' => fn(HasMany $query) => $query->orderBy('name')]),
            'can' => [
                'edit' => Gate::check('owns-universe', $universe),
            ],
        ]);
    }

    public function create(Universe $universe): Response
    {
        $this->authorize('update', $universe);

        return Inertia::render('Series/Create', [
            'universe' => $universe,
        ]);
    }

    public function store(SeriesCreateRequest $request, Universe $universe): RedirectResponse
    {
        $this->authorize('update', $universe);

        $universe->series()->create($request->validated());

        return redirect(route('universes.series.index', [$universe]))
            ->with('notice', 'Series created');
    }

    public function show(Universe $universe, Series $series): Response
    {
        $this->authorize('view', $universe);

        return Inertia::render('Series/View', [
            'universe' => $universe,
            'series' => $series,
        ]);
    }

    public function edit(Universe $universe, Series $series): Response
    {
        $this->authorize('update', $universe);

        return Inertia::render('Series/Edit', [
            'universe' => $universe,
            'series' => $series
        ]);
    }

    public function update(SeriesCreateRequest $request, Universe $universe, Series $series): RedirectResponse
    {
        $this->authorize('update', $universe);

        $series->update($request->validated());

        return redirect(route('universes.series.index', [$universe]))
            ->with('notice', 'Circuit updated');
    }
}
