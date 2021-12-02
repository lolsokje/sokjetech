<?php

namespace App\Http\Controllers;

use App\Http\Requests\UniverseCreateRequest;
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

    public function index(): Response
    {
        $universes = Universe::visible()->orderBy('name')->get();

        return Inertia::render('Universes/Index', [
            'universes' => $universes->map(function (Universe $universe) {
                return [
                    'name' => $universe->name,
                    'id' => $universe->id,
                    'can' => auth()->user()?->id === $universe->user_id,
                ];
            }),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Universes/Create', [
            'visibilities' => Universe::visibilityLabels(),
        ]);
    }

    public function store(UniverseCreateRequest $request): RedirectResponse
    {
        $request->user()->universes()->create($request->validated());

        return redirect(route('universes.index'));
    }

    /**
     * @throws AuthorizationException
     */
    public function edit(Universe $universe): Response
    {
        $this->authorize('update', $universe);

        return Inertia::render('Universes/Edit', [
            'universe' => $universe,
            'visibilities' => Universe::visibilityLabels(),
        ]);
    }

    /**
     * @throws AuthorizationException
     */
    public function update(UniverseCreateRequest $request, Universe $universe): RedirectResponse
    {
        $this->authorize('update', $universe);

        $universe->update($request->validated());

        return redirect(route('universes.index'));
    }
}
