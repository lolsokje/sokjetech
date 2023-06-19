<?php

namespace App\Http\Controllers;

use App\Actions\GetCircuits;
use App\Http\Requests\Circuit\CircuitCreateRequest;
use App\Http\Requests\Circuit\CircuitUpdateRequest;
use App\Http\Requests\CircuitFilterRequest;
use App\Http\Resources\Circuit\CircuitVariationResource;
use App\Http\Resources\CircuitResource;
use App\Models\Circuit;
use App\Models\Climate;
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
            'circuits' => CircuitResource::collection($circuits),
            'links' => $circuits->linkCollection(),
            'filters' => $request->validated(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Circuits/Create', [
            'climates' => Climate::with('conditions')->get(),
        ]);
    }

    public function store(CircuitCreateRequest $request): RedirectResponse
    {
        /** @var Circuit $circuit */
        $circuit = $request->user()->circuits()->create($request->circuitData());

        $circuit->variations()->create($request->variationData());

        return redirect(route('circuits.index'))
            ->with('notice', 'Circuit created');
    }

    public function edit(Circuit $circuit): Response
    {
        $this->authorize('alter', $circuit);

        return Inertia::render('Circuits/Edit', [
            'circuit' => $circuit,
            'variations' => CircuitVariationResource::collection($circuit->variations),
            'climates' => Climate::with('conditions')->get(),
        ]);
    }

    public function update(CircuitUpdateRequest $request, Circuit $circuit): RedirectResponse
    {
        $this->authorize('alter', $circuit);

        $circuit->update($request->validated());

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
