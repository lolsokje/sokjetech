<?php

namespace App\Actions\Circuits;

use App\Http\Resources\Circuit\CircuitVariationResource;
use App\Models\Circuit;
use App\Models\CircuitVariation;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GetCircuitVariationsForSharedCircuit
{
    public function handle(string $circuitName): AnonymousResourceCollection
    {
        $variations = CircuitVariation::query()
            ->whereIn('circuit_id', Circuit::where('name', $circuitName)->pluck('id'))
            ->get();

        return CircuitVariationResource::collection($variations);
    }
}
