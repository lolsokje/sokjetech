<?php

namespace App\Http\Controllers;

use App\Actions\GetUniverses;
use App\Enums\UniverseVisibility;
use App\Http\Requests\UniverseCreateRequest;
use App\Http\Requests\UniverseFilterRequest;
use App\Http\Resources\UniverseResource;
use App\Models\Universe;
use Illuminate\Auth\Access\AuthorizationException;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UniverseController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth'])->only('create', 'edit');
    }

    public function index(UniverseFilterRequest $request): Response
    {
        $universes = (new GetUniverses($request))->handle();

        return Inertia::render('Universes/Index', [
            'links' => $universes->linkCollection(),
            'universes' => UniverseResource::collection($universes)->toArray($request),
            'filters' => $request->validated(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Universes/Create', [
            'visibilities' => UniverseVisibility::labels(),
        ]);
    }

    public function store(UniverseCreateRequest $request): RedirectResponse
    {
        $request->user()->universes()->create($request->validated());

        return redirect(route('universes.index'))
            ->with('notice', 'Universe created');
    }

    /**
     * @throws AuthorizationException
     */
    public function edit(Universe $universe): Response
    {
        $this->authorize('update', $universe);

        return Inertia::render('Universes/Edit', [
            'universe' => $universe,
            'visibilities' => UniverseVisibility::labels(),
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function update(UniverseCreateRequest $request, Universe $universe): RedirectResponse
    {
        $this->authorize('update', $universe);

        $universe->update($request->validated());

        return redirect(route('universes.index'))
            ->with('notice', 'Universe updated');
    }
}
