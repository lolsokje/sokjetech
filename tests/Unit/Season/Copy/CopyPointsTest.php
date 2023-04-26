<?php

use App\Actions\Season\Copy\CopyPoints;
use App\Exceptions\InvalidSeasonRequirements;
use App\Models\PointDistribution;
use App\Models\PointSystem;
use App\Models\User;

it('copies point system and distribution', function () {
    [$season, $newSeason] = preparePoints();

    $this->assertDatabaseCount('point_systems', 1);
    $this->assertDatabaseCount('point_distributions', 5);

    (new CopyPoints($season, $newSeason))->handle();

    $this->assertDatabaseCount('point_systems', 2);
    $this->assertDatabaseCount('point_distributions', 10);

    $this->assertDatabaseHas('point_systems', ['season_id' => $newSeason->id]);
    $this->assertDatabaseHas('point_distributions', ['point_system_id' => $newSeason->fresh()->pointSystem->id]);
});

test('a point system and distribution must exist when copying', function () {
    [$season, $newSeason] = preparePoints();

    $season->pointDistribution()->delete();
    $season->pointSystem()->delete();

    $this->assertDatabaseCount('point_systems', 0);
    $this->assertDatabaseCount('point_distributions', 0);

    (new CopyPoints($season, $newSeason))->handle();
})->throws(InvalidSeasonRequirements::class);

it('removes existing point system and distributions from the new season before copying', function () {
    [$season, $newSeason] = preparePoints();

    (new CopyPoints($season, $newSeason))->handle();
    (new CopyPoints($season, $newSeason))->handle();

    $this->assertDatabaseCount('point_systems', 2);
    $this->assertDatabaseCount('point_distributions', 10);
});

function preparePoints(): array
{
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $system = PointSystem::factory()->for($season)->create();
    PointDistribution::factory(5)->for($system)->create();

    $newSeason = createSeasonForUser($user);

    test()->actingAs($user);

    return [$season, $newSeason];
}
