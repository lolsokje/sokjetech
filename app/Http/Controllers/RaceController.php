<?php

namespace App\Http\Controllers;

use App\Actions\Races\DeleteRace;
use App\Actions\Races\StoreRaceAction;
use App\Actions\Races\UpdateRaceOrderAction;
use App\Enums\RaceType;
use App\Http\Requests\RaceCreateRequest;
use App\Http\Resources\CircuitResource;
use App\Http\Resources\Race\CalendarRaceResource;
use App\Http\Resources\Race\EditRaceResource;
use App\Http\Resources\RaceOverviewPoleResource;
use App\Http\Resources\RaceOverviewWinnerResource;
use App\Http\Resources\Season\CalendarSeasonResource;
use App\Models\Climate;
use App\Models\Race;
use App\Models\Season;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\JsonResponse;
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
            'races' => fn (HasMany $query) => $query->with('circuit')->orderBy('order'),
        ])->append('can_start', 'can_complete');

        return Inertia::render('Races/Index', [
            'season' => new CalendarSeasonResource($season),
            'races' => CalendarRaceResource::collection($season->races),
            'poles' => RaceOverviewPoleResource::collection($season->poles)->toArray(request()),
            'winners' => RaceOverviewWinnerResource::collection($season->winners)->toArray(request()),
            'next_race_id' => $season->nextRace()?->id,
        ]);
    }

    public function create(Season $season): Response
    {
        $this->authorize('update', $season->universe);

        $circuits = auth()->user()->circuits()->with('variations', 'defaultClimate')->get();

        return Inertia::render('Races/Create', [
            'season' => $season,
            'circuits' => CircuitResource::collection($circuits),
            'climates' => Climate::with('conditions')->get(),
            'types' => RaceType::labels(),
        ]);
    }

    public function store(RaceCreateRequest $request, Season $season): RedirectResponse
    {
        $this->authorize('update', $season->universe);

        (new StoreRaceAction($request, $season))->handle();

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

        $circuits = auth()->user()->circuits()->with('variations', 'defaultClimate')->get();

        return Inertia::render('Races/Edit', [
            'season' => $season,
            'race' => new EditRaceResource($race),
            'circuits' => CircuitResource::collection($circuits),
            'climates' => Climate::with('conditions')->get(),
            'types' => RaceType::labels(),
        ]);
    }

    public function update(RaceCreateRequest $request, Season $season, Race $race): RedirectResponse
    {
        $this->authorize('update', $season->universe);

        $race->update($request->validated());

        return redirect(route('seasons.races.index', [$season]))
            ->with('notice', 'Race updated');
    }

    public function destroy(Season $season, Race $race): JsonResponse
    {
        $this->authorize('update', $season->universe);

        (new DeleteRace($season, $race))->handle();

        return response()->json([], JsonResponse::HTTP_NO_CONTENT);
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
