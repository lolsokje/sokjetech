<?php

namespace App\Http\Controllers;

use App\Actions\Ratings\UpdateDriverRatings;
use App\Http\Requests\DriverReliabilityUpdateRequest;
use App\Models\Season;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UpdateDriverReliabilityController extends Controller
{
    public function __invoke(DriverReliabilityUpdateRequest $request, Season $season): RedirectResponse
    {
        $this->authorize('update', $season->universe);
        $this->middleware(['race_in_progress']);

        (new UpdateDriverRatings($request->validated('drivers'), 'reliability'))->handle();

        return redirect(route('seasons.development.reliability.drivers', [$season]))
            ->with('notice', 'Driver reliabilities updated successfully');
    }
}
