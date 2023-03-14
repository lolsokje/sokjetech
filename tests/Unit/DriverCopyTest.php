<?php

use App\Actions\Season\CopyDrivers;
use App\Models\Racer;
use App\Models\User;

it('copies drivers', function () {
    [$season, $newSeason] = prepareDrivers();

    (new CopyDrivers($season, $newSeason, false))->handle();

    $this->assertDatabaseCount('racers', 6);

    $this->assertDatabaseHas('racers', ['season_id' => $season->id]);
    $this->assertDatabaseHas('racers', ['season_id' => $newSeason->id]);
});

it('does not copy ratings when not requested to', function () {
    [$season, $newSeason] = prepareDrivers();

    (new CopyDrivers($season, $newSeason, false))->handle();

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

    (new CopyDrivers($season, $newSeason, true))->handle();

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

    (new CopyDrivers($season, $newSeason, true))->handle();
    (new CopyDrivers($season, $newSeason, true))->handle();

    $this->assertDatabaseCount('racers', 6);

    $this->assertDatabaseHas('racers', ['season_id' => $season->id]);
    $this->assertDatabaseHas('racers', ['season_id' => $newSeason->id]);
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
