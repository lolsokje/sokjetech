<?php

use App\Actions\Season\Copy\CopyDrivers;
use App\Exceptions\InvalidSeasonRequirements;
use App\Models\Driver;
use App\Models\Racer;
use App\Models\User;

it('copies drivers', function () {
    [$season, $newSeason] = prepareDrivers();

    (new CopyDrivers($season, $newSeason))->handle();

    $this->assertDatabaseCount('racers', 6);

    $this->assertDatabaseHas('racers', ['season_id' => $season->id]);
    $this->assertDatabaseHas('racers', ['season_id' => $newSeason->id]);
});

test('drivers must exist in the old season before copying', function () {
    [$season, $newSeason] = prepareDrivers();

    $season->drivers()->delete();

    $this->assertDatabaseCount('racers', 0);

    (new CopyDrivers($season, $newSeason))->handle();
})->throws(InvalidSeasonRequirements::class);

it('does not copy ratings when not requested to', function () {
    [$season, $newSeason] = prepareDrivers();

    (new CopyDrivers($season, $newSeason))->handle();

    $this->assertDatabaseCount('racers', 6);

    $this->assertDatabaseHas('racers', [
        'season_id' => $season->id,
        'rating' => $season->drivers->first()->rating,
        'reliability' => $season->drivers->first()->reliability,
    ]);

    $this->assertDatabaseHas('racers', [
        'season_id' => $newSeason->id,
        'rating' => 0,
        'reliability' => 0,
    ]);
});

it('copies ratings when requested to', function () {
    [$season, $newSeason] = prepareDrivers();

    (new CopyDrivers($season, $newSeason))->handle(copyRatings: true);

    $this->assertDatabaseCount('racers', 6);

    $this->assertDatabaseHas('racers', [
        'season_id' => $season->id,
        'rating' => $season->drivers->first()->rating,
        'reliability' => $season->drivers->first()->reliability,
    ]);

    $this->assertDatabaseHas('racers', [
        'season_id' => $newSeason->id,
        'rating' => $season->drivers->first()->rating,
        'reliability' => $season->drivers->first()->reliability,
    ]);
});

it('removes existing drivers from the new season before creating new teams', function () {
    [$season, $newSeason] = prepareDrivers();

    (new CopyDrivers($season, $newSeason))->handle(copyRatings: true);
    (new CopyDrivers($season, $newSeason))->handle(copyRatings: true);

    $this->assertDatabaseCount('racers', 6);

    $this->assertDatabaseHas('racers', ['season_id' => $season->id]);
    $this->assertDatabaseHas('racers', ['season_id' => $newSeason->id]);
});

it('does not copy retired drivers to new seasons', function () {
    [$season, $newSeason] = prepareDrivers();

    Driver::first()->update(['retired' => true]);

    (new CopyDrivers($season, $newSeason))->handle();

    $this->assertDatabaseCount('racers', 5);

    $this->assertCount(3, $season->drivers);
    $this->assertCount(2, $newSeason->drivers);
});

function prepareDrivers(): array
{
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    Racer::factory(3)->for($season)->create();

    $newSeason = createSeasonForUser($user);

    test()->actingAs($user);

    return [$season, $newSeason];
}
