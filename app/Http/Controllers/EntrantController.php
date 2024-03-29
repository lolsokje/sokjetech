<?php

namespace App\Http\Controllers;

use App\Actions\DeleteEntrant;
use App\Http\Requests\EntrantCreateRequest;
use App\Models\Entrant;
use App\Models\Season;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class EntrantController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth'])->only('create', 'edit');
        $this->middleware(['race_in_progress'])->except('index');
        $this->middleware('season_started')->only('destroy');
    }

    public function index(Season $season): Response
    {
        return Inertia::render('Entrants/Index', [
            'season' => $season->load([
                'entrants' => fn (HasMany $query) => $query->orderBy('full_name')->with('engine'),
            ])->append('has_active_race'),
        ]);
    }

    public function create(Season $season): Response
    {
        $this->authorize('update', $season->universe);

        return Inertia::render('Entrants/Create', [
            'season' => $season,
            'teams' => $season->teams,
            'engines' => $season->engines,
        ]);
    }

    public function store(EntrantCreateRequest $request, Season $season): RedirectResponse
    {
        $this->authorize('update', $season->universe);

        $season->entrants()->create($request->validated());

        return redirect(route('seasons.entrants.index', [$season]))
            ->with('notice', 'Entrant added to season');
    }

    public function edit(Season $season, Entrant $entrant): Response
    {
        $this->authorize('update', $season->universe);

        return Inertia::render('Entrants/Edit', [
            'season' => $season,
            'teams' => $season->teams,
            'engines' => $season->engines,
            'entrant' => $entrant,
        ]);
    }

    public function update(EntrantCreateRequest $request, Season $season, Entrant $entrant): RedirectResponse
    {
        $this->authorize('update', $season->universe);

        $entrant->update($request->validated());

        return redirect(route('seasons.entrants.index', [$season]))
            ->with('notice', 'Entrant updated');
    }

    public function destroy(Season $season, Entrant $entrant): RedirectResponse
    {
        (new DeleteEntrant($entrant))->handle();

        return to_route('seasons.entrants.index', $season)
            ->with('notice', "\"$entrant->full_name\" deleted");
    }
}
