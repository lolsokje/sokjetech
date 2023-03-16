<?php

use App\Actions\Races\Configuration\StoreQualifyingFormat;
use App\DataTransferObjects\Configuration\QualifyingFormatConfiguration;
use App\Models\Season;

it('stores the qualifying format', function () {
    $season = Season::factory()->create();
    $configuration = new QualifyingFormatConfiguration('single_session', [
        'runs_per_session' => 1,
        'min_rng' => 1,
        'max_rng' => 2,
    ]);

    (new StoreQualifyingFormat($configuration, $season))->handle();

    $this->assertNotNull($season->fresh()->qualifyingFormat);
});
