<?php

namespace App\Http\Controllers;

use App\Actions\StoreEntrantRacers;
use App\Http\Requests\RacerCreateRequest;
use App\Models\Entrant;
use App\Models\Racer;
use App\Models\Season;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RacerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth'])->only('create', 'edit');
        $this->middleware(['race_in_progress'])->except('index');
    }

    public function index(Season $season): Response
    {
        return Inertia::render('Racers/Index', [
            'season' => $season->append('has_active_race'),
            'racers' => $season->activeRacers()
                ->with('entrant.engine', 'driver')
                ->get(),
        ]);
    }

    public function create(Season $season, Entrant $entrant): Response
    {
        $this->authorize('update', $season->universe);

        return Inertia::render('Racers/Create', [
            'season' => $season,
            'entrant' => $entrant->load([
                'activeRacers' => function (HasMany $query) {
                    return $query
                        ->orderBy('active', 'DESC')
                        ->with('driver:id,first_name,last_name,dob');
                },
            ]),
            'drivers' => $season->availableDrivers(),
            'numbers' => $season->pickedNumbers(),
        ]);
    }

    public function store(RacerCreateRequest $request, Season $season, Entrant $entrant): RedirectResponse
    {
        $this->authorize('update', $season->universe);

        (new StoreEntrantRacers($request, $season, $entrant))->handle();

        return redirect(route('seasons.racers.create', [$season, $entrant]))
            ->with('notice', 'Driver added to team and season');
    }

    public function show(Season $season, Racer $racer): Response
    {
        $this->authorize('view', $season->universe);

        return Inertia::render('Racers/Show', [$season, $racer]);
    }

    public function edit(Season $season, Racer $racer): Response
    {
        $this->authorize('update', $season->universe);

        return Inertia::render('Racers/Edit', [$season, $racer]);
    }

    public function update(RacerCreateRequest $request, Season $season, Racer $racer): RedirectResponse
    {
        $this->authorize('update', $season->universe);

        $racer->update($request->validated());

        return redirect(route('seasons.racers.index', [$season]))
            ->with('notice', 'Driver updated');
    }
}
