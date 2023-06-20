<?php

namespace App\Http\Controllers;

use App\Actions\Circuits\CopyCircuitVariations;
use App\Models\Circuit;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CopyCircuitController extends Controller
{
    public function __invoke(Request $request, Circuit $circuit): Response
    {
        $this->middleware('auth');

        $newCircuit = $circuit->replicate();
        $newCircuit->user()->associate(auth()->user());
        $newCircuit->save();

        if ($request->has('variations')) {
            (new CopyCircuitVariations)->handle($newCircuit, $request->get('variations'));
        }

        return to_route('database.circuits.index')
            ->with('notice', 'Circuit copied');
    }
}
