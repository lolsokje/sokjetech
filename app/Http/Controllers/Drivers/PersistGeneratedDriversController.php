<?php

namespace App\Http\Controllers\Drivers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Drivers\PersistDriversRequest;
use App\Models\Universe;
use App\Support\DriverGenerator\Generator;

class PersistGeneratedDriversController extends Controller
{
    public function __invoke(Universe $universe, PersistDriversRequest $request)
    {
        (new Generator())->persist($universe, $request->get('drivers'));

        return to_route('universes.drivers.generate.show', [
            'universe' => $universe,
        ])
            ->with('notice', 'Drivers saved');
    }
}
