<?php

use App\Models\Driver;
use App\Models\Entrant;
use App\Models\Lineup;
use App\Models\Season;
use App\Models\User;

test('a universe owner can add drivers to entrants', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $drivers = Driver::factory(2)->for($season->universe)->create();
    $entrant = Entrant::factory()->for($season)->create();

    $this->actingAs($user)
        ->post(route('seasons.lineups.store', [$season, $entrant]), [
            'drivers' => [
                [
                    'driver_id' => $drivers[0]->id,
                    'number' => 2,
                ],
                [
                    'driver_id' => $drivers[1]->id,
                    'number' => 3,
                ],
            ],
        ])
        ->assertRedirect(route('seasons.lineups.index', [$season]));

    $this->assertDatabaseCount('lineups', 2);
    $this->assertCount(2, $entrant->drivers);
    $this->assertCount(2, $season->drivers);
});

test('a driver number must be unique in the current season', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $drivers = Driver::factory(3)->for($season->universe)->create();
    $entrant = Entrant::factory()->for($season)->create();
    Lineup::factory()->for($season)->create([
        'driver_id' => $drivers[0]->id,
        'entrant_id' => $entrant->id,
        'number' => 2,
    ]);

    $this->actingAs($user)
        ->post(route('seasons.lineups.store', [$season, $entrant]), [
            'drivers' => [
                [
                    'driver_id' => $drivers[1]->id,
                    'number' => 2,
                ],
                [
                    'driver_id' => $drivers[2]->id,
                    'number' => 3,
                ],
            ],
        ])
        ->assertSessionHasErrors(['drivers.0.number' => 'The number 2 is already used by another active driver this season.']);
});

test('the driver numbers must be unique in the current request', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $drivers = Driver::factory(3)->for($season->universe)->create();
    $entrant = Entrant::factory()->for($season)->create();
    Lineup::factory()->for($season)->create([
        'driver_id' => $drivers[0]->id,
        'entrant_id' => $entrant->id,
        'number' => 2,
    ]);

    $this->actingAs($user)
        ->post(route('seasons.lineups.store', [$season, $entrant]), [
            'drivers' => [
                [
                    'driver_id' => $drivers[0]->id,
                    'number' => 3,
                ],
                [
                    'driver_id' => $drivers[1]->id,
                    'number' => 3,
                ],
            ],
        ])
        ->assertSessionHasErrors(['drivers' => 'The number 3 is used by more than one driver in the current lineup.']);
});

test('a driver can only be active for one team in a season', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $driver = Driver::factory()->for($season->universe)->create();
    $entrant = Entrant::factory()->for($season)->create();
    Lineup::factory()->for($season)->create([
        'driver_id' => $driver->id,
        'entrant_id' => $entrant->id
    ]);

    $this->actingAs($user)
        ->post(route('seasons.lineups.store', [$season, $entrant]), [
            'drivers' => [
                [
                    'driver_id' => $driver->id,
                    'number' => 3,
                ],
            ],
        ])
        ->assertSessionHasErrors(['drivers.0.driver_id' => "Driver $driver->fullName is already active for another team this season."]);
});

test('the drivers in a request must be unique', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $driver = Driver::factory()->for($season->universe)->create();
    $entrant = Entrant::factory()->for($season)->create();

    $this->actingAs($user)
        ->post(route('seasons.lineups.store', [$season, $entrant]), [
            'drivers' => [
                [
                    'driver_id' => $driver->id,
                    'number' => 2,
                ],
                [
                    'driver_id' => $driver->id,
                    'number' => 2,
                ],
            ],
        ])
        ->assertSessionHasErrors(['drivers' => "Driver $driver->fullName has been added more than once."]);
});

test('an unauthenticated user cannot add drivers to entrants', function () {
    $season = Season::factory()->create();
    $driver = Driver::factory()->for($season->universe)->create();
    $entrant = Entrant::factory()->for($season)->create();

    $this->post(route('seasons.lineups.store', [$season, $entrant]), [
        'drivers' => [
            [
                'driver_id' => $driver->id,
                'number' => 2,
            ],
        ],
    ])
        ->assertForbidden();

    $this->assertDatabaseCount('lineups', 0);
    $this->assertCount(0, $entrant->drivers);
    $this->assertCount(0, $season->drivers);
});

test('an authenticated user cannot add drivers to another users entrants', function () {
    $user = User::factory()->create();
    $season = Season::factory()->create();
    $driver = Driver::factory()->for($season->universe)->create();
    $entrant = Entrant::factory()->for($season)->create();

    $this->actingAs($user)
        ->post(route('seasons.lineups.store', [$season, $entrant]), [
            'drivers' => [
                [
                    'driver_id' => $driver->id,
                    'number' => 2,
                ],
            ],
        ])
        ->assertForbidden();

    $this->assertDatabaseCount('lineups', 0);
    $this->assertCount(0, $entrant->drivers);
    $this->assertCount(0, $season->drivers);
});

test('a universe owner can remove drivers from entrants', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $drivers = Driver::factory(3)->for($season->universe)->create();
    $entrant = Entrant::factory()->for($season)->create();
    Lineup::factory()->for($entrant)->create(['driver_id' => $drivers[0]->id]);
    Lineup::factory()->for($entrant)->create(['driver_id' => $drivers[1]->id]);

    $this->actingAs($user)
        ->post(route('seasons.lineups.store', [$season, $entrant]), [
            'drivers' => [
                [
                    'driver_id' => $drivers[0]->id,
                    'number' => 2,
                ],
            ],
        ])
        ->assertRedirect(route('seasons.lineups.index', [$season]));

    $this->assertCount(2, $entrant->drivers);
    $this->assertCount(1, $entrant->lineup);
});
