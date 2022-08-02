<?php

namespace App\Http\Controllers;

use App\Actions\Races\Stints\StoreStintsAction;
use App\Actions\Races\Stints\UpdateStintsAction;
use App\Actions\Races\StoreRaceAction;
use App\Actions\Races\UpdateRaceOrderAction;
use App\Http\Requests\RaceCreateRequest;
use App\Http\Resources\RaceOverviewPoleResource;
use App\Http\Resources\RaceOverviewWinnerResource;
use App\Models\Race;
use App\Models\Season;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RaceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth'])->only('create', 'edit', 'reorder');
        $this->middleware(['season_started'])->except('index', 'show');
    }

    public function index(Season $season): Response
    {
        $season->load([
            'poles',
            'winners',
            'races' => fn (HasMany $query) => $query->orderBy('order'),
        ]);

        return Inertia::render('Races/Index', [
            'season' => $season,
            'poles' => RaceOverviewPoleResource::collection($season->poles)->toArray(request()),
            'winners' => RaceOverviewWinnerResource::collection($season->winners)->toArray(request()),
            'next_race_id' => $season->nextRace()?->id,
        ]);
    }

    public function create(Season $season): Response
    {
        $this->authorize('update', $season->universe);

        return Inertia::render('Races/Create', [
            'season' => $season,
            'circuits' => auth()->user()->circuits,
        ]);
    }

    public function store(RaceCreateRequest $request, Season $season): RedirectResponse
    {
        $this->authorize('update', $season->universe);

        $race = (new StoreRaceAction($request, $season))->handle();

        (new StoreStintsAction($race, $request->stints()))->handle();

        return redirect(route('seasons.races.index', [$season]))
            ->with('notice', 'Race created');
    }

    public function show(Season $season, Race $race): Response
    {
        return Inertia::render('Races/Show', [
            'season' => $season,
            'race' => $race,
        ]);
    }

    public function edit(Season $season, Race $race): Response
    {
        $this->authorize('update', $season->universe);

        return Inertia::render('Races/Edit', [
            'season' => $season,
            'race' => $race->load('stints'),
            'circuits' => auth()->user()->circuits,
        ]);
    }

    public function update(RaceCreateRequest $request, Season $season, Race $race): RedirectResponse
    {
        $this->authorize('update', $season->universe);

        $race->update($request->raceData());

        (new UpdateStintsAction($race, $request->stints()))->handle();

        return redirect(route('seasons.races.index', [$season]))
            ->with('notice', 'Race updated');
    }

    public function reorder(Season $season): Response
    {
        $this->authorize('update', $season->universe);

        return Inertia::render('Races/Reorder', [
            'season' => $season->load(['races' => fn (HasMany $query) => $query->orderBy('order')]),
        ]);
    }

    public function order(Request $request, Season $season): RedirectResponse
    {
        $this->authorize('update', $season->universe);

        $races = collect($request->get('races'));

        (new UpdateRaceOrderAction($races))->handle();

        return redirect(route('seasons.races.index', [$season]))
            ->with('notice', 'Races reordered');
    }
}
