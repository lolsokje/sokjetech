<?php

use App\Exceptions\InvalidSeasonRequirements;
use App\Models\Race;
use App\Models\Season;
use App\Models\Stint;
use App\Models\User;
use Illuminate\Validation\UnauthorizedException;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\post;
use function Pest\Laravel\withoutExceptionHandling;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertNull;

const RACE_COUNT = 3;

test('unauthorised users cannot copy races', function () {
    $season = Season::factory()->create();
    prepareSeasonRaces($season);

    $newSeason = Season::factory()->create();

    post(route('seasons.settings.copy.races', [$newSeason]), [
        'season_id' => $season->id,
    ])
        ->assertForbidden();

    actingAs(User::factory()->create())
        ->post(route('seasons.settings.copy.races', [$newSeason]), [
            'season_id' => $season->id,
        ])
        ->assertForbidden();

    assertCount(0, $newSeason->fresh()->races);
});

it('requires a source season ID when copying races', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.settings.copy.races', [$season]))
        ->assertInvalid(['season_id' => 'required']);
});

test('the source season needs to be owned by the universe owner', function () {
    withoutExceptionHandling();
    $user = User::factory()->create();
    $season = Season::factory()->create();
    prepareSeasonRaces($season);
    $newSeason = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.settings.copy.races', [$newSeason]), [
            'season_id' => $season->id,
        ]);
})->throws(UnauthorizedException::class);

test('a source season needs races before copying', function () {
    withoutExceptionHandling();
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $newSeason = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.settings.copy.races', [$newSeason]), [
            'season_id' => $season->id,
        ]);

    assertCount(0, $newSeason->fresh()->races);
    assertDatabaseCount('stints', 0);
})->throws(InvalidSeasonRequirements::class);

it('clears existing races before copying new races', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    prepareSeasonRaces($season);

    $newSeason = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.settings.copy.races', [$newSeason]), [
            'season_id' => $season->id,
        ])
        ->assertCreated();

    assertCount(RACE_COUNT, $newSeason->races);

    actingAs($user)
        ->post(route('seasons.settings.copy.races', [$newSeason]), [
            'season_id' => $season->id,
        ])
        ->assertCreated();

    assertCount(RACE_COUNT * 3, Stint::all());
    assertCount(RACE_COUNT, $newSeason->fresh()->races);
});

test('a universe owner can copy races to a new season', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    prepareSeasonRaces($season);

    $newSeason = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.settings.copy.races', [$newSeason]), [
            'season_id' => $season->id,
        ])
        ->assertCreated();

    assertCount(RACE_COUNT, $newSeason->fresh()->races);
});

it('copies race stints when instructed to do so', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    prepareSeasonRaces($season);

    $newSeason = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.settings.copy.races', [$newSeason]), [
            'season_id' => $season->id,
            'copy_stints' => true,
        ])
        ->assertCreated();

    foreach ($newSeason->races->load('stints') as $race) {
        assertCount(3, $race->stints);
    }
});

it('does not copy stints when not instructed to do so', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    prepareSeasonRaces($season);

    $newSeason = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.settings.copy.races', [$newSeason]), [
            'season_id' => $season->id,
        ])
        ->assertCreated();

    foreach ($newSeason->races->load('stints') as $race) {
        assertCount(0, $race->stints);
    }
});

it('does not copy race status', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    prepareSeasonRaces($season);

    $firstRace = $season->races->first();
    $firstRace->update([
        'qualifying_started' => true,
        'qualifying_completed' => true,
        'started' => true,
        'completed' => true,
        'details' => ['test_key' => 'test_value'],
    ]);

    $newSeason = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.settings.copy.races', [$newSeason]), [
            'season_id' => $season->id,
        ])
        ->assertCreated();

    foreach ($newSeason->fresh()->races as $race) {
        assertFalse($race->qualifying_started);
        assertFalse($race->qualifying_completed);
        assertFalse($race->started);
        assertFalse($race->completed);
        assertNull($race->details);
    }
});

it("updates a races' name if it includes the season year", function () {
    $user = User::factory()->create();
    $oldSeason = tap(createSeasonForUser($user), fn (Season $season) => $season->update(['year' => 2021]));;
    $oldSeason->update(['year' => 2021]);
    prepareSeasonRaces($oldSeason);

    $newSeason = tap(createSeasonForUser($user), fn (Season $season) => $season->update(['year' => 2022]));

    $firstRaceOldSeason = $oldSeason->races->first();
    $firstRaceOldSeason->update([
        'name' => "$oldSeason->year Test Grand Prix",
    ]);

    actingAs($user)
        ->post(route('seasons.settings.copy.races', [$newSeason]), [
            'season_id' => $oldSeason->id,
        ])
        ->assertCreated();

    assertEquals("$newSeason->year Test Grand Prix", $newSeason->fresh()->races->first()->name);
});

function prepareSeasonRaces(Season $season): void
{
    Race::factory(RACE_COUNT)->for($season)->create();
}
