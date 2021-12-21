<?php

namespace App\Http\Controllers;

use App\Http\Requests\SeasonCreateRequest;
use App\Models\Season;
use App\Models\Series;
use Gate;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SeasonController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth'])->only('create', 'edit');
    }

    public function index(Series $series): Response
    {
        return Inertia::render('Seasons/Index', [
            'series' => $series->load(['seasons' => fn(HasMany $query) => $query->orderBy('year')]),
            'can' => [
                'edit' => Gate::check('owns-universe', $series->universe),
            ],
        ]);
    }

    public function create(Series $series): Response
    {
        $this->authorize('update', $series->universe);

        return Inertia::render('Seasons/Create', [
            'series' => $series,
        ]);
    }

    public function store(SeasonCreateRequest $request, Series $series): RedirectResponse
    {
        $this->authorize('update', $series->universe);

        $series->seasons()->create($request->validated());

        return redirect(route('series.seasons.index', [$series]))
            ->with('notice', 'Season created');
    }

    public function show(Series $series, Season $season): Response
    {
        $this->authorize('update', $series->universe);

        return Inertia::render('Seasons/View', [
            'series' => $series,
            'season' => $season,
        ]);
    }

    public function edit(Series $series, Season $season): Response
    {
        $this->authorize('update', $series->universe);

        return Inertia::render('Seasons/Edit', [
            'series' => $series,
            'season' => $season,
        ]);
    }

    public function update(SeasonCreateRequest $request, Series $series, Season $season): RedirectResponse
    {
        $this->authorize('update', $series->universe);

        $season->update($request->validated());

        return redirect(route('series.seasons.index', [$series]))
            ->with('notice', 'Season updated');
    }
}
