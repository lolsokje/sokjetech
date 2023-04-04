<?php

namespace App\Http\Controllers\Drivers;

use App\Actions\Season\Drivers\PersistGeneratedDrivers;
use App\Http\Controllers\Controller;
use App\Http\Requests\Drivers\PersistDriversRequest;
use App\Models\Universe;

class PersistGeneratedDriversController extends Controller
{
    public function __invoke(Universe $universe, PersistDriversRequest $request)
    {
        (new PersistGeneratedDrivers)->handle($universe, $request->get('drivers'));

        return to_route('universes.drivers.generate.show', [
            'universe' => $universe,
        ])
            ->with('notice', 'Drivers saved');
    }
}
