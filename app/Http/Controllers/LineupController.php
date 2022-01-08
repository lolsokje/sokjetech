<?php

namespace App\Http\Controllers;

use App\Http\Requests\LineupCreateRequest;
use App\Models\Entrant;
use App\Models\Lineup;
use App\Models\Season;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class LineupController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth'])->only('create', 'edit');
    }

    public function index(Season $season): Response
    {
        return Inertia::render('Lineups/Index', [
            'season' => $season->load(['drivers' => fn(HasMany $query) => $query->with('entrant')]),
        ]);
    }

    public function create(Season $season): Response
    {
        $this->authorize('update', $season->universe);

        return Inertia::render('Lineups/Create', [
            'season' => $season,
        ]);
    }

    public function store(LineupCreateRequest $request, Season $season, Entrant $entrant): RedirectResponse
    {
        $this->authorize('update', $season->universe);

        $drivers = $request->validated()['drivers'];

        $entrant->drivers()->each(fn(Lineup $driver) => $driver->update(['active' => false]));

        foreach ($drivers as $driver) {
            $entrant->drivers()->updateOrCreate(
                ['driver_id' => $driver['driver_id']],
                [
                    'season_id' => $season->id,
                    'number' => $driver['number'],
                    'active' => true,
                ],
            );
        }

        return redirect(route('seasons.lineups.index', [$season]));
    }

    public function show(Season $season, Lineup $lineup): Response
    {
        $this->authorize('view', $season->universe);

        return Inertia::render('Lineups/Show', [$season, $lineup]);
    }

    public function edit(Season $season, Lineup $lineup): Response
    {
        $this->authorize('update', $season->universe);

        return Inertia::render('Lineups/Edit', [$season, $lineup]);
    }

    public function update(LineupCreateRequest $request, Season $season, Lineup $lineup): RedirectResponse
    {
        $this->authorize('update', $season->universe);

        $lineup->update($request->validated());

        return redirect(route('seasons.lineups.index', [$season]));
    }
}
