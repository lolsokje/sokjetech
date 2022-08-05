<?php

namespace App\Http\Controllers;

use App\Actions\Ratings\UpdateDriverRatings;
use App\Http\Requests\DriverRatingUpdateRequest;
use App\Models\Season;
use Symfony\Component\HttpFoundation\RedirectResponse;

class UpdateDriverRatingsController extends Controller
{
    public function __invoke(DriverRatingUpdateRequest $request, Season $season): RedirectResponse
    {
        $this->authorize('update', $season->universe);
        $this->middleware(['race_in_progress']);

        (new UpdateDriverRatings($request->validated('drivers')))->handle();

        return redirect(route('seasons.development.drivers', [$season]))
            ->with('notice', 'Driver ratings updated successfully');
    }
}
