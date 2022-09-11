<?php

namespace App\Http\Controllers;

use App\Actions\GetCircuits;
use App\Http\Requests\CircuitCreateRequest;
use App\Http\Requests\CircuitFilterRequest;
use App\Http\Resources\CircuitResource;
use App\Models\Circuit;
use Exception;
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
        $circuits = (new GetCircuits($request))->handle();

        return Inertia::render('Circuits/Index', [
            'circuits' => CircuitResource::collection($circuits)->toArray($request),
            'links' => $circuits->linkCollection(),
            'filters' => $request->validated(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Circuits/Create');
    }

    public function store(CircuitCreateRequest $request): RedirectResponse
    {
        $request->user()->circuits()->create($request->data());

        return redirect(route('circuits.index'))
            ->with('notice', 'Circuit created');
    }

    public function edit(Circuit $circuit): Response
    {
        $this->authorize('alter', $circuit);

        return Inertia::render('Circuits/Edit', [
            'circuit' => $circuit,
        ]);
    }

    public function update(CircuitCreateRequest $request, Circuit $circuit): RedirectResponse
    {
        $this->authorize('alter', $circuit);

        $circuit->update($request->data());

        return redirect(route('circuits.index'))
            ->with('notice', 'Circuit updated');
    }

    public function destroy(Circuit $circuit): RedirectResponse
    {
        $this->authorize('alter', $circuit);

        if ($circuit->races_count > 0) {
            throw new Exception("Circuits can't be deleted once they've been used in a race");
        }

        $circuit->delete();

        return back()->with('notice', 'Circuit removed');
    }
}
