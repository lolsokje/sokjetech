<?php

namespace App\Http\Controllers;

use App\Http\Requests\CircuitCreateRequest;
use App\Models\Circuit;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;

class CircuitController extends Controller
{
    public function index()
    {
    }

    public function store(CircuitCreateRequest $request): RedirectResponse
    {
        $request->user()->circuits()->create($request->validated());

        return redirect(route('circuits.index'));
    }

    /**
     * @param CircuitCreateRequest $request
     * @param Circuit $circuit
     *
     * @return RedirectResponse
     * @throws AuthorizationException
     */
    public function update(CircuitCreateRequest $request, Circuit $circuit): RedirectResponse
    {
        $this->authorize('alter', $circuit);

        $circuit->update($request->validated());

        return redirect(route('circuits.index'));
    }
}
