<?php

namespace App\Http\Controllers;

use App\Actions\SetQualifyingFormat;
use App\Http\Requests\QualifyingConfigurationRequest;
use App\Models\Season;
use Symfony\Component\HttpFoundation\RedirectResponse;

class StoreQualifyingSettingsController extends Controller
{
    public function __invoke(Season $season, QualifyingConfigurationRequest $request): RedirectResponse
    {
        (new SetQualifyingFormat($season, $request))->handle();

        return to_route('series.seasons.show', [$season->series, $season])
            ->with('notice', 'Updated qualifying format');
    }
}
