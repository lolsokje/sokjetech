<?php

namespace App\Http\Controllers;

use App\Actions\Races\Configuration\StoreQualifyingFormat;
use App\DataTransferObjects\Configuration\QualifyingFormatConfiguration;
use App\Http\Requests\QualifyingConfigurationRequest;
use App\Models\Season;
use Symfony\Component\HttpFoundation\RedirectResponse;

class StoreQualifyingSettingsController extends Controller
{
    public function __invoke(Season $season, QualifyingConfigurationRequest $request): RedirectResponse
    {
        (new StoreQualifyingFormat(
            new QualifyingFormatConfiguration(
                $request->validated('selected_format'),
                $request->validated('format_details'),
            ),
            $season
        ))->handle();

        return to_route('series.seasons.show', [$season->series, $season])
            ->with('notice', 'Updated qualifying format');
    }
}
