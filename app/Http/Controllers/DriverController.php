<?php

namespace App\Http\Controllers;

use App\Http\Requests\DriverCreateRequest;
use App\Models\Driver;
use App\Models\Universe;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DriverController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth'])->only('create', 'edit');
    }

    public function index(Universe $universe): Response
    {
        return Inertia::render('Drivers/Index', [
            'universe' => $universe->load(['drivers' => fn(HasMany $query) => $query->orderBy('last_name')]),
        ]);
    }

    public function create(Universe $universe): Response
    {
        return Inertia::render('Drivers/Create', [
            'universe' => $universe,
        ]);
    }

    public function store(DriverCreateRequest $request, Universe $universe): RedirectResponse
    {
        $this->authorize('update', $universe);

        $universe->drivers()->create($request->validated());

        return redirect(route('universes.drivers.index', [$universe]))
            ->with('notice', 'Driver created');
    }

    public function edit(Universe $universe, Driver $driver): Response
    {
        return Inertia::render('Drivers/Edit', [
            'universe' => $universe,
            'driver' => $driver
        ]);
    }

    public function update(DriverCreateRequest $request, Universe $universe, Driver $driver): RedirectResponse
    {
        $this->authorize('update', $universe);

        $driver->update($request->validated());

        return redirect(route('universes.drivers.index', [$universe]))
            ->with('notice', 'Driver updated');
    }
}
