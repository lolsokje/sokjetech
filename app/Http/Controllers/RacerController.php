<?php

namespace App\Http\Controllers;

use App\Actions\StoreEntrantRacers;
use App\Http\Requests\RacerCreateRequest;
use App\Http\Resources\Season\AvailableDriversCollection;
use App\Http\Resources\Season\CreateRacerEntrantResource;
use App\Models\Entrant;
use App\Models\Season;
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
        $this->authorize('view', $season->universe);

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

        $entrant->load([
            'activeRacers' => [
                'season',
                'driver',
            ],
        ]);

        return Inertia::render('Racers/Create', [
            'season' => $season,
            'entrant' => CreateRacerEntrantResource::array($entrant),
            'drivers' => (new AvailableDriversCollection($season->availableDrivers(), $season))->toArray(request()),
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
}
