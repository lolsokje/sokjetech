<?php

namespace App\Http\Controllers;

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
    }

    public function index(Season $season): Response
    {
        return Inertia::render('Racers/Index', [
            'season' => $season,
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

        $drivers = $request->validated()['drivers'];

        $entrant->allRacers()->each(fn (Racer $driver) => $driver->update(['active' => false]));

        foreach ($drivers as $driver) {
            $entrant->allRacers()->updateOrCreate(
                ['driver_id' => $driver['driver_id']],
                [
                    'season_id' => $season->id,
                    'number' => $driver['number'],
                    'active' => true,
                ],
            );
        }

        return redirect(route('seasons.racers.index', [$season]))
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
