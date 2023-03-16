<?php

use App\Models\Racer;
use App\Models\Season;
use App\Models\User;
use Illuminate\Validation\UnauthorizedException;

const DRIVER_COUNT = 3;

test('unauthorized users cannot copy drivers', function () {
    $season = Season::factory()->create();

    $this->post(route('seasons.settings.copy.drivers', [$season]))
        ->assertForbidden();

    $this->actingAs(User::factory()->create())
        ->post(route('seasons.settings.copy.drivers', [$season]))
        ->assertForbidden();
});

it('requires a source season id when copying drivers', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    $this->actingAs($user)
        ->post(route('seasons.settings.copy.drivers', [$season]))
        ->assertInvalid(['season_id' => 'required']);
});

test('the source season needs to be owned by the universe owner', function () {
    $this->withoutExceptionHandling();
    $user = User::factory()->create();
    $season = Season::factory()->create();
    $newSeason = createSeasonForUser($user);

    $this->actingAs($user)
        ->post(route('seasons.settings.copy.drivers', [$newSeason]), [
            'season_id' => $season->id,
        ]);
})->throws(UnauthorizedException::class);

test('a source season needs drivers before copying', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $newSeason = createSeasonForUser($user);

    $this->actingAs($user)
        ->post(route('seasons.settings.copy.drivers', [$newSeason]), [
            'season_id' => $season->id,
        ])
        ->assertJson(['error' => 'No drivers added to the selected season']);
});

it('clears existing drivers before copying', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    prepareSeasonDrivers($season);

    $newSeason = createSeasonForUser($user);
    prepareSeasonDrivers($newSeason);

    $this->assertCount(DRIVER_COUNT, $newSeason->drivers);

    $this->actingAs($user)
        ->post(route('seasons.settings.copy.drivers', [$newSeason]), [
            'season_id' => $season->id,
        ])
        ->assertCreated();

    $newSeason = $newSeason->fresh();

    $this->assertCount(DRIVER_COUNT, $newSeason->drivers);
});

test('a universe owner can copy drivers', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    prepareSeasonDrivers($season);

    $this->assertCount(DRIVER_COUNT, $season->drivers);

    $newSeason = createSeasonForUser($user);

    $this->assertCount(0, $newSeason->drivers);

    $this->actingAs($user)
        ->post(route('seasons.settings.copy.drivers', [$newSeason]), [
            'season_id' => $season->id,
        ])
        ->assertCreated();

    $season = $season->fresh();
    $newSeason = $newSeason->fresh();

    $this->assertCount(DRIVER_COUNT, $season->drivers);
    $this->assertCount(DRIVER_COUNT, $newSeason->drivers);
});

it('does not copy driver ratings when not instructed to', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    prepareSeasonDrivers($season);

    $newSeason = createSeasonForUser($user);

    $this->actingAs($user)
        ->post(route('seasons.settings.copy.drivers', $newSeason), [
            'season_id' => $season->id,
        ])
        ->assertCreated();

    $newSeason = $newSeason->fresh();

    foreach ($newSeason->drivers as $driver) {
        $this->assertEquals(0, $driver->rating);
        $this->assertEquals(0, $driver->reliability);
    }
});

it('copies driver ratings when instructed to do so', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    prepareSeasonDrivers($season);

    $newSeason = createSeasonForUser($user);

    $this->actingAs($user)
        ->post(route('seasons.settings.copy.drivers', $newSeason), [
            'season_id' => $season->id,
            'copy_ratings' => true,
        ])
        ->assertCreated();

    foreach ($newSeason->drivers as $newDriver) {
        $oldDriver = Racer::where('season_id', $season->id)->where('driver_id', $newDriver->driver_id)->first();
        $this->assertEquals($oldDriver->rating, $newDriver->rating);
        $this->assertEquals($oldDriver->reliability, $newDriver->reliability);
    }
});

function prepareSeasonDrivers(Season $season): void
{
    Racer::factory(DRIVER_COUNT)->for($season)->create();
}
