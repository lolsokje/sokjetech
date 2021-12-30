<?php

namespace App\Http\Controllers;

use App\Http\Requests\CircuitCreateRequest;
use App\Http\Requests\CircuitFilterRequest;
use App\Models\Circuit;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CircuitController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth'])->only('create', 'edit');
    }

    public function index(CircuitFilterRequest $request): Response
    {
        $circuits = auth()->user()->circuits()
            ->search($request->get('search'))
            ->sort($request->get('field'), $request->get('direction'))
            ->paginate(15);

        return Inertia::render('Circuits/Index', [
            'circuits' => $circuits,
            'filters' => $request->all(['search', 'field', 'direction']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Circuits/Create');
    }

    public function store(CircuitCreateRequest $request): RedirectResponse
    {
        $request->user()->circuits()->create($request->validated());

        return redirect(route('circuits.index'))
            ->with('notice', 'Circuit created');
    }

    /**
     * @param  Circuit  $circuit
     *
     * @return Response
     * @throws AuthorizationException
     */
    public function edit(Circuit $circuit): Response
    {
        $this->authorize('alter', $circuit);

        return Inertia::render('Circuits/Edit', [
            'circuit' => $circuit,
        ]);
    }

    /**
     * @param  CircuitCreateRequest  $request
     * @param  Circuit  $circuit
     *
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function update(CircuitCreateRequest $request, Circuit $circuit): RedirectResponse
    {
        $this->authorize('alter', $circuit);

        $circuit->update($request->validated());

        return redirect(route('circuits.index'))
            ->with('notice', 'Circuit updated');
    }
}
