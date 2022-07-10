<?php

namespace App\Http\Controllers;

use App\Http\Requests\DriverRatingUpdateRequest;
use App\Models\Racer;
use App\Models\Season;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UpdateDriverRatingsController extends Controller
{
    public function __invoke(DriverRatingUpdateRequest $request, Season $season): RedirectResponse
    {
        $this->authorize('update', $season->universe);

        $drivers = collect($request->get('drivers'));

        $drivers->each(function ($driver) {
            Racer::query()
                ->find($driver['id'])
                ->update(['rating' => $driver['new']]);
        });

        return redirect(route('seasons.development.drivers', [$season]))
            ->with('notice', 'Driver ratings updated successfully');
    }
}
