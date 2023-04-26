<?php

namespace App\Http\Controllers;

use App\Http\Resources\Development\DriverResource;
use App\Models\Season;
use Inertia\Inertia;
use Inertia\Response;

class ShowDriverDevelopmentPageController extends Controller
{
    public function __invoke(Season $season): Response
    {
        $this->authorize('update', $season->universe);

        $drivers = $season->activeRacers()->with(['driver', 'entrant', 'season'])->get();
        $drivers = DriverResource::collection($drivers)->toArray(request());

        return Inertia::render('Development/Drivers', [
            'season' => $season->append('has_active_race'),
            'drivers' => $drivers,
        ]);
    }
}
