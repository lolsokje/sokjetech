<?php

use App\Actions\Season\Copy\CopyRaces;
use App\Exceptions\InvalidSeasonRequirements;
use App\Models\Race;
use App\Models\User;

it('copies races', function () {
    [$season, $newSeason] = prepareRaces();

    (new CopyRaces($season, $newSeason))->handle();

    $this->assertDatabaseCount('races', 6);

    $this->assertDatabaseHas('races', ['season_id' => $season->id]);
    $this->assertDatabaseHas('races', ['season_id' => $newSeason->id]);
});

test('races must exist in the old season before copying', function () {
    [$season, $newSeason] = prepareRaces();

    $season->races()->delete();

    $this->assertDatabaseCount('races', 0);

    (new CopyRaces($season, $newSeason))->handle();
})->throws(InvalidSeasonRequirements::class);

it('does not copy stints when not requested to', function () {
    [$season, $newSeason] = prepareRaces(true);

    $this->assertDatabaseCount('stints', 9);

    (new CopyRaces($season, $newSeason))->handle();

    $this->assertDatabaseCount('stints', 9);

    $this->assertDatabaseMissing('stints', ['race_id' => $newSeason->races()->first()->id]);
});

it('copies stints when requested to', function () {
    [$season, $newSeason] = prepareRaces(true);

    $this->assertDatabaseCount('stints', 9);

    (new CopyRaces($season, $newSeason))->handle(copyStints: true);

    $this->assertDatabaseCount('stints', 18);

    $this->assertDatabaseHas('stints', ['race_id' => $newSeason->races()->first()->id]);
});

it('removes existing races from the new season before copying', function () {
    [$season, $newSeason] = prepareRaces();

    $this->assertDatabaseCount('races', 3);

    (new CopyRaces($season, $newSeason))->handle();
    (new CopyRaces($season, $newSeason))->handle();

    $this->assertDatabaseCount('races', 6);

    $this->assertDatabaseHas('races', ['season_id' => $season->id]);
    $this->assertDatabaseHas('races', ['season_id' => $newSeason->id]);
});

function prepareRaces(?bool $withStints = false): array
{
    $user = User::factory()->create();

    $season = createSeasonForUser($user);
    $newSeason = createSeasonForUser($user);

    if ($withStints) {
        Race::factory(3)->for($season)->withStints()->create();
    } else {
        Race::factory(3)->for($season)->create();
    }

    test()->actingAs($user);

    return [$season, $newSeason];
}
