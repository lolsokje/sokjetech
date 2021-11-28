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
    public function index(): Response
    {
        return Inertia::render('Index');
    }

    public function store(UniverseCreateRequest $request): RedirectResponse
    {
        $request->user()->universes()->create($request->validated());

        return redirect(route('universes.index'));
    }

    /**
     * @param UniverseCreateRequest $request
     * @param Universe $universe
     *
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function update(UniverseCreateRequest $request, Universe $universe): RedirectResponse
    {
        $this->authorize('update', $universe);

        $universe->update($request->validated());

        return redirect(route('universes.index'));
    }
}
