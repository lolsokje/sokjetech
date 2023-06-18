<?php

namespace App\Http\Controllers\Circuits;

use App\Http\Controllers\Controller;
use App\Http\Requests\CircuitVariationRequest;
use App\Http\Resources\Circuit\CircuitVariationResource;
use App\Models\Circuit;
use App\Models\CircuitVariation;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CircuitVariationController extends Controller
{
    public function create(Circuit $circuit): Response
    {
        $this->authorize('alter', $circuit);

        return Inertia::render('Circuits/Variations/Create', [
            'circuit' => $circuit,
        ]);
    }

    public function store(CircuitVariationRequest $request, Circuit $circuit): RedirectResponse
    {
        $this->authorize('alter', $circuit);

        $circuit->variations()->create($request->validated());

        return to_route('circuits.edit', $circuit)
            ->with('notice', 'Circuit variation created');
    }

    public function edit(Circuit $circuit, CircuitVariation $variation): Response
    {
        $this->authorize('alter', $circuit);

        return Inertia::render('Circuits/Variations/Edit', [
            'circuit' => $circuit,
            'variation' => new CircuitVariationResource($variation),
        ]);
    }

    public function update(
        CircuitVariationRequest $request,
        Circuit $circuit,
        CircuitVariation $variation,
    ): RedirectResponse {
        $this->authorize('alter', $circuit);

        $variation->update($request->validated());

        return to_route('circuits.variations.edit', [
            'circuit' => $circuit,
            'variation' => $variation,
        ])->with('notice', 'Variation updated');
    }
}
