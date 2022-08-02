<?php

namespace App\Http\Controllers;

use App\Http\Requests\DriverReliabilityUpdateRequest;
use App\Models\Racer;
use App\Models\Season;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UpdateDriverReliabilityController extends Controller
{
    public function __invoke(DriverReliabilityUpdateRequest $request, Season $season): RedirectResponse
    {
        $this->authorize('update', $season->universe);
        $this->middleware(['race_in_progress']);

        $drivers = collect($request->get('drivers'));

        $drivers->each(function ($driver) {
            Racer::query()
                ->where('id', $driver['id'])
                ->update(['reliability' => $driver['new']]);
        });

        return redirect(route('seasons.development.reliability.drivers', [$season]))
            ->with('notice', 'Driver reliabilities updated successfully');
    }
}
