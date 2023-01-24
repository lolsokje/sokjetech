<?php

namespace App\Http\Controllers;

use App\Http\Requests\DriverCopyRequest;
use App\Models\Driver;
use App\Models\Universe;
use Illuminate\Http\RedirectResponse;

class CopyDriverController extends Controller
{
    public function __invoke(DriverCopyRequest $request, Driver $driver): RedirectResponse
    {
        $universe = Universe::find($request->validated('universe_id'));
        $this->authorize('update', $universe);

        $newDriver = $driver->replicate();
        $newDriver->universe()->associate($universe);
        $newDriver->save();

        return to_route('database.drivers.index')
            ->with('notice', 'Driver copied');
    }
}
