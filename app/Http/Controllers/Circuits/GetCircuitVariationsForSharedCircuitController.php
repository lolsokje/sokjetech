<?php

namespace App\Http\Controllers\Circuits;

use App\Actions\Circuits\GetCircuitVariationsForSharedCircuit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class GetCircuitVariationsForSharedCircuitController
{
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        $circuit = $request->get('circuit');

        return (new GetCircuitVariationsForSharedCircuit)->handle($circuit);
    }
}
