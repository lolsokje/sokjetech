<?php

use App\Actions\Races\Configuration\StorePointDistributions;
use App\Actions\Races\Configuration\StorePointsSystemConfiguration;
use App\DataTransferObjects\Configuration\PointSystemConfiguration;
use App\DataTransferObjects\PointsData;
use App\Models\PointSystem;
use App\Models\Season;

it('stores the point system configuration', function () {
    $season = Season::factory()->create();

    $configuration = new PointSystemConfiguration(
        polePositionPointAwarded: true,
        polePositionPointAmount: 1,
    );

    (new StorePointsSystemConfiguration(
        $configuration,
        $season,
    ))->handle();

    $season = $season->fresh();

    $this->assertTrue($season->pointSystem->pole_position_point_awarded);
    $this->assertEquals(1, $season->pointSystem->pole_position_point_awarded);
});

it('stores the point distribution', function () {
    $season = Season::factory()->create();

    $points = collect([
        new PointsData(['position' => 1, 'points' => 10]),
        new PointsData(['position' => 2, 'points' => 5]),
        new PointsData(['position' => 3, 'points' => 1]),
    ]);

    $pointSystem = PointSystem::factory()->for($season)->create();

    (new StorePointDistributions($pointSystem, $points))->handle();

    $this->assertDatabaseCount('point_distributions', 3);
    $this->assertCount(3, $season->pointDistribution);
});
