<?php

use App\Actions\Season\Copy\CopyEngines;
use App\Exceptions\InvalidSeasonRequirements;
use App\Models\EngineSeason;
use App\Models\User;

it('copies engines', function () {
    [$season, $newSeason] = prepareEngines();

    (new CopyEngines($season, $newSeason, false))->handle();

    $this->assertDatabaseCount('engine_seasons', 6);

    $this->assertDatabaseHas('engine_seasons', ['season_id' => $season->id]);
    $this->assertDatabaseHas('engine_seasons', ['season_id' => $newSeason->id]);
});

test('engines must exist in the old season before copying', function () {
    [$season, $newSeason] = prepareEngines();

    $season->engines()->delete();

    $this->assertDatabaseCount('engine_seasons', 0);

    (new CopyEngines($season, $newSeason))->handle();
})->throws(InvalidSeasonRequirements::class);

it('does not copy ratings when not requested to', function () {
    [$season, $newSeason] = prepareEngines();

    (new CopyEngines($season, $newSeason, false))->handle();

    $this->assertDatabaseCount('engine_seasons', 6);

    $this->assertDatabaseHas('engine_seasons', [
        'season_id' => $season->id,
        'rating' => $season->engines->first()->rating,
        'reliability' => $season->engines->first()->reliability,
    ]);

    $this->assertDatabaseHas('engine_seasons', [
        'season_id' => $newSeason->id,
        'rating' => 0,
        'reliability' => 0,
    ]);
});

it('copies ratings when requested to', function () {
    [$season, $newSeason] = prepareEngines();

    (new CopyEngines($season, $newSeason))->handle(copyRatings: true);

    $this->assertDatabaseCount('engine_seasons', 6);

    $this->assertDatabaseHas('engine_seasons', [
        'season_id' => $season->id,
        'rating' => $season->engines->first()->rating,
        'reliability' => $season->engines->first()->reliability,
    ]);

    $this->assertDatabaseHas('engine_seasons', [
        'season_id' => $newSeason->id,
        'rating' => $season->engines->first()->rating,
        'reliability' => $season->engines->first()->reliability,
    ]);
});

it('removes existing engines from the new season before creating new teams', function () {
    [$season, $newSeason] = prepareEngines();

    (new CopyEngines($season, $newSeason))->handle(copyRatings: true);
    (new CopyEngines($season, $newSeason))->handle(copyRatings: true);

    $this->assertDatabaseCount('engine_seasons', 6);

    $this->assertDatabaseHas('engine_seasons', ['season_id' => $season->id]);
    $this->assertDatabaseHas('engine_seasons', ['season_id' => $newSeason->id]);
});

function prepareEngines(): array
{
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    EngineSeason::factory(3)->for($season)->create();

    $newSeason = createSeasonForUser($user);

    test()->actingAs($user);

    return [$season, $newSeason];
}
