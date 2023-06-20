<?php

namespace App\Actions\Circuits;

use App\Models\Circuit;
use App\Models\CircuitVariation;

class CopyCircuitVariations
{
    public function handle(Circuit $circuit, array $variationIds): void
    {
        $variations = CircuitVariation::whereIn('id', $variationIds)->get();

        foreach ($variations as $variation) {
            $newVariation = $variation->replicate();
            $newVariation->circuit()->associate($circuit);
            $newVariation->save();
        }
    }
}
