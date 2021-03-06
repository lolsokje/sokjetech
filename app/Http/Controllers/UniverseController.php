<?php

namespace App\Http\Controllers;

use App\Enums\UniverseVisibility;
use App\Http\Requests\UniverseCreateRequest;
use App\Models\Universe;
use Gate;
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
                    'can' => [
                        'edit' => Gate::check('owns-universe', $universe),
                    ],
                ];
            }),
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

    public function show(Universe $universe): RedirectResponse
    {
        $this->authorize('view', $universe);

        return redirect(route('universes.series.index', [$universe]));
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
