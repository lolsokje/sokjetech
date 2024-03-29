<?php

use App\Enums\UniverseVisibility;
use App\Models\Driver;
use App\Models\Entrant;
use App\Models\Racer;
use App\Models\Season;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('only shows available drivers on the lineup create page', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $drivers = Driver::factory(3)->for($season->universe)->create();
    $entrants = Entrant::factory(2)->for($season)->create();
    Racer::factory()->for($season)->create([
        'driver_id' => $drivers[0]->id,
        'entrant_id' => $entrants[0]->id,
    ]);

    $this->actingAs($user)
        ->get(route('seasons.racers.create', [$season, $entrants[1]]))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Racers/Create')
                ->has('drivers', 2),
        );
});

it('does not show retired drivers on the racer create page', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    Driver::factory(3)->for($season->universe)->create();
    Driver::first()->update(['retired' => true]);
    $entrant = Entrant::factory()->create();

    $this->actingAs($user)
        ->get(route('seasons.racers.create', [$season, $entrant]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('drivers', 2));
});

test('a universe owner can add drivers to entrants', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $drivers = Driver::factory(2)->for($season->universe)->create();
    $entrant = Entrant::factory()->for($season)->create();

    $this->actingAs($user)
        ->post(route('seasons.racers.store', [$season, $entrant]), [
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
        ->assertRedirect(route('seasons.racers.create', [$season, $entrant]));

    $this->assertDatabaseCount('racers', 2);
    $this->assertCount(2, $entrant->allRacers);
    $this->assertCount(2, $season->drivers);
});

test('retired drivers can not be added to entrants', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $driver = Driver::factory()->for($season->universe)->retired()->create();
    $entrant = Entrant::factory()->for($season)->create();

    $this->actingAs($user)
        ->from(route('seasons.racers.create', [$season, $entrant]))
        ->post(route('seasons.racers.store', [$season, $entrant]), [
            'drivers' => [
                ['driver_id' => $driver->id, 'number' => 1],
            ],
        ])
        ->assertRedirect(route('seasons.racers.create', [$season, $entrant]))
        ->assertSessionHasErrors([
            'drivers.0.driver_id' => 'This driver is retired and can not be added to an entrant.',
        ]);

    $this->assertDatabaseCount('racers', 0);
    $this->assertCount(0, $entrant->allRacers);
    $this->assertCount(0, $season->drivers);
});

test('a driver number must be unique in the current season', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $drivers = Driver::factory(3)->for($season->universe)->create();
    $entrants = Entrant::factory(2)->for($season)->create();
    Racer::factory()->for($season)->create([
        'driver_id' => $drivers[0]->id,
        'entrant_id' => $entrants[0]->id,
        'number' => 2,
    ]);

    $this->actingAs($user)
        ->post(route('seasons.racers.store', [$season, $entrants[1]]), [
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
    Racer::factory()->for($season)->create([
        'driver_id' => $drivers[0]->id,
        'entrant_id' => $entrant->id,
        'number' => 2,
    ]);

    $this->actingAs($user)
        ->post(route('seasons.racers.store', [$season, $entrant]), [
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

test('a driver already added to an entrant can be saved again with the same driver and number', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $drivers = Driver::factory(2)->for($season->universe)->create();
    $entrant = Entrant::factory()->for($season)->create();
    Racer::factory()->for($season)->create([
        'driver_id' => $drivers[0]->id,
        'entrant_id' => $entrant->id,
        'number' => 2,
    ]);

    $this->actingAs($user)
        ->post(route('seasons.racers.store', [$season, $entrant]), [
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
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('seasons.racers.create', [$season, $entrant]));
});

test('a driver can only be active for one team in a season', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $driver = Driver::factory()->for($season->universe)->create();
    $entrants = Entrant::factory(2)->for($season)->create();
    Racer::factory()->for($season)->create([
        'driver_id' => $driver->id,
        'entrant_id' => $entrants[0]->id,
    ]);

    $this->actingAs($user)
        ->post(route('seasons.racers.store', [$season, $entrants[1]]), [
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
        ->post(route('seasons.racers.store', [$season, $entrant]), [
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

    $this->post(route('seasons.racers.store', [$season, $entrant]), [
        'drivers' => [
            [
                'driver_id' => $driver->id,
                'number' => 2,
            ],
        ],
    ])
        ->assertForbidden();

    $this->assertDatabaseCount('racers', 0);
    $this->assertCount(0, $entrant->allRacers);
    $this->assertCount(0, $season->drivers);
});

test('an authenticated user cannot add drivers to another users entrants', function () {
    $user = User::factory()->create();
    $season = Season::factory()->create();
    $driver = Driver::factory()->for($season->universe)->create();
    $entrant = Entrant::factory()->for($season)->create();

    $this->actingAs($user)
        ->post(route('seasons.racers.store', [$season, $entrant]), [
            'drivers' => [
                [
                    'driver_id' => $driver->id,
                    'number' => 2,
                ],
            ],
        ])
        ->assertForbidden();

    $this->assertDatabaseCount('racers', 0);
    $this->assertCount(0, $entrant->allRacers);
    $this->assertCount(0, $season->drivers);
});

test('a universe owner can remove drivers from entrants', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $drivers = Driver::factory(3)->for($season->universe)->create();
    $entrant = Entrant::factory()->for($season)->create();
    Racer::factory()->for($entrant)->create(['driver_id' => $drivers[0]->id]);
    Racer::factory()->for($entrant)->create(['driver_id' => $drivers[1]->id]);

    $this->actingAs($user)
        ->post(route('seasons.racers.store', [$season, $entrant]), [
            'drivers' => [
                [
                    'driver_id' => $drivers[0]->id,
                    'number' => 2,
                ],
            ],
        ])
        ->assertRedirect(route('seasons.racers.create', [$season, $entrant]));

    $this->assertCount(2, $entrant->allRacers);
    $this->assertCount(1, $entrant->activeRacers);
});

it('shows the racer index page for universe owners', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    Racer::factory(3)->for($season)->create();

    $this->actingAs($user)
        ->get(route('seasons.racers.index', $season))
        ->assertOk();
});

it('does not show the racer index page for unauthorised users', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user, UniverseVisibility::PRIVATE);

    $this->get(route('seasons.racers.index', $season))
        ->assertForbidden();

    $this->actingAs(User::factory()->create())
        ->get(route('seasons.racers.index', $season))
        ->assertForbidden();
});

it('shows all active racers on the racer index page', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    Racer::factory(3)->for($season)->create();

    $this->actingAs($user)
        ->get(route('seasons.racers.index', $season))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Racers/Index')
            ->has('season', fn (Assert $prop) => $prop
                ->where('has_active_race', false)
                ->etc())
            ->has('racers', 3));
});
