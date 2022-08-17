<?php

use App\Exceptions\InvalidSeasonRequirements;
use App\Models\EngineSeason;
use App\Models\Entrant;
use App\Models\Racer;
use App\Models\Season;
use App\Models\User;
use Illuminate\Validation\UnauthorizedException;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;
use function Pest\Laravel\withoutExceptionHandling;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;

const ENGINE_COUNT = 2;
const TEAM_COUNT = 2;
const DRIVER_COUNT = 1;
const TOTAL_TEAM_COUNT = ENGINE_COUNT * TEAM_COUNT;
const TOTAL_DRIVER_COUNT = TOTAL_TEAM_COUNT * DRIVER_COUNT;

test('unauthorized users cannot copy season settings', function () {
    $season = Season::factory()->create();

    post(route('seasons.settings.copy.teams', [$season]))
        ->assertForbidden();

    actingAs(User::factory()->create())
        ->post(route('seasons.settings.copy.teams', [$season]))
        ->assertForbidden();
});

it('requires a source season ID when copying teams, drivers and engines', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.settings.copy.teams', [$season]))
        ->assertInvalid(['season_id' => 'required']);
});

test('the source season needs to be owned by the universe owner', function () {
    withoutExceptionHandling();
    $user = User::factory()->create();
    $season = Season::factory()->create();
    prepareSeasonLineups($season);
    $newSeason = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.settings.copy.teams', [$newSeason]), [
            'season_id' => $season->id,
        ]);
})->throws(UnauthorizedException::class);

test('a source season needs engines, teams and drivers before copying', function () {
    withoutExceptionHandling();
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $newSeason = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.settings.copy.teams', [$newSeason]), [
            'season_id' => $season->id,
        ]);
})->throws(InvalidSeasonRequirements::class);

it('clears existing engines, teams and drivers before copying', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    prepareSeasonLineups($season);

    $newSeason = createSeasonForUser($user);
    prepareSeasonLineups($newSeason);

    assertCount(ENGINE_COUNT, $newSeason->engines);
    assertCount(TOTAL_TEAM_COUNT, $newSeason->entrants);
    assertCount(TOTAL_DRIVER_COUNT, $newSeason->drivers);

    actingAs($user)
        ->post(route('seasons.settings.copy.teams', [$newSeason]), [
            'season_id' => $season->id,
        ])
        ->assertCreated();

    $newSeason = $newSeason->fresh();

    assertCount(ENGINE_COUNT, $newSeason->engines);
    assertCount(TOTAL_TEAM_COUNT, $newSeason->entrants);
    assertCount(TOTAL_DRIVER_COUNT, $newSeason->drivers);
});

test('a universe owner can copy teams, drivers and engines', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    prepareSeasonLineups($season);

    assertCount(ENGINE_COUNT, $season->engines);
    assertCount(TOTAL_TEAM_COUNT, $season->entrants);
    assertCount(TOTAL_DRIVER_COUNT, $season->drivers);

    $newSeason = createSeasonForUser($user);

    assertCount(0, $newSeason->engines);
    assertCount(0, $newSeason->entrants);
    assertCount(0, $newSeason->drivers);

    actingAs($user)
        ->post(route('seasons.settings.copy.teams', [$newSeason]), [
            'season_id' => $season->id,
        ])
        ->assertCreated();

    $season = $season->fresh();
    $newSeason = $newSeason->fresh();

    assertCount(ENGINE_COUNT, $season->engines);
    assertCount(ENGINE_COUNT, $newSeason->engines);
    assertCount(TOTAL_TEAM_COUNT, $season->entrants);
    assertCount(TOTAL_TEAM_COUNT, $newSeason->entrants);
    assertCount(TOTAL_DRIVER_COUNT, $season->drivers);
    assertCount(TOTAL_DRIVER_COUNT, $newSeason->drivers);

    foreach ($newSeason->entrants as $entrant) {
        $entrant->load('activeRacers');
        assertCount(DRIVER_COUNT, $entrant->activeRacers);
    }
});

it('copies new engines to new teams', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    prepareSeasonLineups($season);

    $newSeason = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.settings.copy.teams', [$newSeason]), [
            'season_id' => $season->id,
        ])
        ->assertCreated();

    foreach ($newSeason->fresh()->entrants as $entrant) {
        $oldEntrant = $season->entrants()->where('team_id', $entrant->team_id)->first();
        $oldEngine = $oldEntrant->engine;
        $newEngine = $newSeason->engines()->where('base_engine_id', $oldEngine->base_engine_id)->first();
        assertEquals($newEngine->id, $entrant->engine_id);
    }
});

it('does not copy engine, team and driver ratings when not instructed to', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    prepareSeasonLineups($season);

    $newSeason = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.settings.copy.teams', $newSeason), [
            'season_id' => $season->id,
        ])
        ->assertCreated();

    $newSeason = $newSeason->fresh();

    foreach ($newSeason->engines as $engine) {
        assertEquals(0, $engine->rating);
        assertEquals(0, $engine->reliability);
    }

    foreach ($newSeason->entrants as $entrant) {
        assertEquals(0, $entrant->rating);
        assertEquals(0, $entrant->reliability);
    }

    foreach ($newSeason->drivers as $driver) {
        assertEquals(0, $driver->rating);
        assertEquals(0, $driver->reliability);
    }
});

it('copies engine, team and driver ratings when instructed to do so', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    prepareSeasonLineups($season);

    $newSeason = createSeasonForUser($user);

    actingAs($user)
        ->post(route('seasons.settings.copy.teams', $newSeason), [
            'season_id' => $season->id,
            'copy_ratings' => true,
        ])
        ->assertCreated();

    foreach ($newSeason->engines as $newEngine) {
        // Since each engine is created with its own base engine, we can use that to fetch the old engine from the old season
        $oldEngine = EngineSeason::where('season_id', $season->id)->where('base_engine_id', $newEngine->base_engine_id)->first();
        assertEquals($oldEngine->rating, $newEngine->rating);
        assertEquals($oldEngine->reliability, $newEngine->reliability);
    }

    foreach ($newSeason->entrants as $newEntrant) {
        // Since each engine is created with its own base team, we can use that to fetch the old team from the old season
        $oldEntrant = Entrant::where('season_id', $season->id)->where('team_id', $newEntrant->team_id)->first();
        assertEquals($oldEntrant->rating, $newEntrant->rating);
        assertEquals($oldEntrant->reliability, $newEntrant->reliability);
    }

    foreach ($newSeason->drivers as $newRacer) {
        // Since each racer is created with its own base driver, we can use that to fetch the old driver from the old season
        $oldRacer = Racer::where('season_id', $season->id)->where('driver_id', $newRacer->driver_id)->first();
        assertEquals($oldRacer->rating, $newRacer->rating);
        assertEquals($oldRacer->reliability, $newRacer->reliability);
    }
});

function prepareSeasonLineups(Season $season): void
{
    $engines = EngineSeason::factory(ENGINE_COUNT)->for($season)->create();
    $engines->each(fn (EngineSeason $engine) => Entrant::factory(TEAM_COUNT)->for($season)->create(['engine_id' => $engine->id]));
    $entrants = $season->entrants;
    $entrants->each(fn (Entrant $entrant) => Racer::factory(DRIVER_COUNT)->for($entrant)->for($season)->create());
}
