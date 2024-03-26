<?php

namespace App\Http\Controllers\RaceWeekend;

use App\Actions\Races\Results\CreateQualifyingResults;
use App\Http\Controllers\Controller;
use App\Models\Race;
use DB;
use Illuminate\Http\RedirectResponse;

class StartQualifyingController extends Controller
{
    public function __invoke(
        Race $race,
        CreateQualifyingResults $createQualifyingResults,
    ): RedirectResponse {
        $this->authorize('update', $race->season->universe);

        if ($race->qualifying_started) {
            return to_route('weekend.qualifying', $race);
        }

        DB::transaction(function () use ($race, $createQualifyingResults) {
            $race->update(['qualifying_started' => true]);

            $createQualifyingResults->handle($race);
        });

        return to_route('weekend.qualifying', $race);
    }
}
