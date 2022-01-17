<?php

namespace App\Http\Controllers;

use App\Http\Requests\RaceCreateRequest;
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
    }

    public function index(Season $season): Response
    {
        return Inertia::render('Races/Index', [
            'season' => $season->load(['races' => fn(HasMany $query) => $query->orderBy('order')]),
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

        $lastRace = $season->races()->latest('order')->first();
        $order = $lastRace ? $lastRace->order + 1 : 1;

        $data = array_merge(['order' => $order], $request->validated());
        $season->races()->create($data);

        return redirect(route('seasons.races.index', [$season]))
            ->with('notice', 'Race created');
    }

    public function show(Season $season, Race $race): Response
    {
        return Inertia::render('Races/View', [
            'season' => $season,
            'race' => $race,
        ]);
    }

    public function edit(Season $season, Race $race): Response
    {
        $this->authorize('update', $season->universe);

        return Inertia::render('Races/Edit', [
            'season' => $season,
            'race' => $race,
            'circuits' => auth()->user()->circuits,
        ]);
    }

    public function update(RaceCreateRequest $request, Season $season, Race $race): RedirectResponse
    {
        $this->authorize('update', $season->universe);

        $race->update($request->validated());

        return redirect(route('seasons.races.index', [$season]));
    }

    public function reorder(Season $season): Response
    {
        $this->authorize('update', $season->universe);

        return Inertia::render('Races/Reorder', [
            'season' => $season->load(['races' => fn(HasMany $query) => $query->orderBy('order')]),
        ]);
    }

    public function order(Request $request, Season $season): RedirectResponse
    {
        $this->authorize('update', $season->universe);

        $races = collect($request->get('races'));

        $races->each(function (array $race) {
            $dbRace = Race::find($race['id']);
            $dbRace->order = $race['order'];
            $dbRace->save();
        });

        return redirect(route('seasons.races.index', [$season]))
            ->with('notice', 'Races reordered');
    }
}
