<?php

namespace App\Http\Controllers;

use App\Actions\GetDrivers;
use App\Http\Requests\DriverCreateRequest;
use App\Http\Requests\DriverFilterRequest;
use App\Http\Resources\DriverResource;
use App\Models\Driver;
use App\Models\Universe;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DriverController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth'])->only('create', 'edit');
    }

    public function index(DriverFilterRequest $request, Universe $universe): Response
    {
        $drivers = (new GetDrivers($request, $universe))->handle();

        return Inertia::render('Drivers/Index', [
            'universe' => $universe,
            'links' => $drivers->linkCollection(),
            'drivers' => DriverResource::collection($drivers)->toArray($request),
            'filters' => $request->validated(),
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

    public function show(Universe $universe, Driver $driver): Response
    {
        $this->authorize('view', $universe);

        return Inertia::render('Drivers/Show', [
            'universe' => $universe,
            'driver' => $driver,
        ]);
    }

    public function edit(Universe $universe, Driver $driver): Response
    {
        return Inertia::render('Drivers/Edit', [
            'universe' => $universe,
            'driver' => $driver,
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
