<?php

use App\Actions\Season\Copy\CopyReliabilityConfiguration;
use App\Exceptions\InvalidSeasonRequirements;
use App\Models\ReliabilityConfiguration;
use App\Models\ReliabilityReason;
use App\Models\User;

it('copies reliability configuration', function () {
    [$season, $newSeason] = prepareConfiguration();

    (new CopyReliabilityConfiguration($season, $newSeason))->handle();

    $this->assertDatabaseCount('reliability_configurations', 2);
    $this->assertDatabaseCount('reliability_reasons', 6);

    $this->assertDatabaseHas('reliability_configurations', ['season_id' => $season->id]);
    $this->assertDatabaseHas('reliability_configurations', ['season_id' => $newSeason->id]);

    $this->assertDatabaseHas('reliability_reasons', ['season_id' => $season->id]);
    $this->assertDatabaseHas('reliability_reasons', ['season_id' => $newSeason->id]);
});

test('a configuration must exist before copying', function () {
    [$season, $newSeason] = prepareConfiguration();

    $season->reliabilityConfiguration()->delete();
    $season->reliabilityReasons()->delete();

    (new CopyReliabilityConfiguration($season, $newSeason))->handle();
})->throws(InvalidSeasonRequirements::class);

it('removes existing configurations from the new season before copying', function () {
    [$season, $newSeason] = prepareConfiguration();

    (new CopyReliabilityConfiguration($season, $newSeason))->handle();
    (new CopyReliabilityConfiguration($season, $newSeason))->handle();

    $this->assertDatabaseCount('reliability_configurations', 2);
    $this->assertDatabaseCount('reliability_reasons', 6);

    $this->assertDatabaseHas('reliability_configurations', ['season_id' => $season->id]);
    $this->assertDatabaseHas('reliability_configurations', ['season_id' => $newSeason->id]);

    $this->assertDatabaseHas('reliability_reasons', ['season_id' => $season->id]);
    $this->assertDatabaseHas('reliability_reasons', ['season_id' => $newSeason->id]);
});

function prepareConfiguration(): array
{
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    ReliabilityConfiguration::factory()->for($season)->create();
    ReliabilityReason::factory(3)->for($season)->create();

    $newSeason = createSeasonForUser($user);

    test()->actingAs($user);

    return [$season, $newSeason];
}
