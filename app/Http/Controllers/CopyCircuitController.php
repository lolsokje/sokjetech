<?php

namespace App\Http\Controllers;

use App\Models\Circuit;
use Symfony\Component\HttpFoundation\Response;

class CopyCircuitController extends Controller
{
    public function __invoke(Circuit $circuit): Response
    {
        $this->middleware('auth');

        $newCircuit = $circuit->replicate();
        $newCircuit->user()->associate(auth()->user());
        $newCircuit->save();

        return to_route('database.circuits.index')
            ->with('notice', 'Circuit copied');
    }
}
