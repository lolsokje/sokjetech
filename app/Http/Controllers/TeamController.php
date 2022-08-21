<?php

namespace App\Http\Controllers;

use App\Actions\GetTeams;
use App\Http\Requests\TeamCreateRequest;
use App\Http\Requests\TeamFilterRequest;
use App\Http\Resources\TeamResource;
use App\Models\Team;
use App\Models\Universe;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class TeamController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth'])->only('create', 'edit');
    }

    public function index(TeamFilterRequest $request, Universe $universe): Response
    {
        $teams = (new GetTeams($request, $universe))->handle();

        return Inertia::render('Teams/Index', [
            'universe' => $universe,
            'links' => $teams->linkCollection(),
            'teams' => TeamResource::collection($teams)->toArray($request),
            'filters' => $request->validated(),
        ]);
    }

    public function create(Universe $universe): Response
    {
        $this->authorize('update', $universe);

        return Inertia::render('Teams/Create', [
            'universe' => $universe,
        ]);
    }

    public function store(TeamCreateRequest $request, Universe $universe): RedirectResponse
    {
        $this->authorize('update', $universe);

        $universe->teams()->create($request->validated());

        return redirect(route('universes.teams.index', [$universe]))
            ->with('notice', 'Team created');
    }

    public function show(Universe $universe, Team $team): Response
    {
        $this->authorize('view', $universe);

        return Inertia::render('Teams/Show', [
            'universe' => $universe,
            'team' => $team,
        ]);
    }

    public function edit(Universe $universe, Team $team): Response
    {
        $this->authorize('update', $universe);

        return Inertia::render('Teams/Edit', [
            'universe' => $universe,
            'team' => $team,
        ]);
    }

    public function update(TeamCreateRequest $request, Universe $universe, Team $team): RedirectResponse
    {
        $this->authorize('update', $universe);

        $team->update($request->validated());

        return redirect(route('universes.teams.index', [$universe]))
            ->with('notice', 'Team updated');
    }
}
