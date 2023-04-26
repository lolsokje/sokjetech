<?php

use App\Actions\Races\Configuration\StoreReliabilityConfiguration;
use App\DataTransferObjects\Configuration\ReliabilityConfiguration;
use App\Models\Season;

it('stores reliability configuration', function () {
    $season = Season::factory()->create();

    $configuration = new ReliabilityConfiguration(
        5,
        15,
        ['team' => ['one', 'two', 'three']],
    );

    (new StoreReliabilityConfiguration($configuration, $season))->handle();

    $season = $season->fresh();

    $this->assertNotNull($season->reliabilityConfiguration);
    $this->assertCount(3, $season->reliabilityReasons);
});
