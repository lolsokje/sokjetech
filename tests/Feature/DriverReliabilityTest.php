<?php

use App\Models\Racer;
use App\Models\Season;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('a universe owner can view the driver reliability page', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    Racer::factory(4)->for($season)->create();

    $this->actingAs($user)
        ->get(route('seasons.development.reliability.drivers', [$season]))
        ->assertOk()
        ->assertInertia(fn(Assert $page) => $page
            ->component('Development/Reliability/Drivers')
            ->has('season')
            ->has('drivers', 4)
        );
});

test('an unauthorized user cant view the driver reliability page', function () {
    $season = Season::factory()->create();
    Racer::factory(4)->for($season)->create();


    $this->get(route('seasons.development.reliability.drivers', [$season]))
        ->assertForbidden();
});

test('an authorized user cant view the driver reliability page for another users universe', function () {
    $season = Season::factory()->create();
    Racer::factory(4)->for($season)->create();

    $this->actingAs(User::factory()->create())
        ->get(route('seasons.development.reliability.drivers', [$season]))
        ->assertForbidden();
});

test('a universe owner can update driver reliability', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    [$racerOne, $racerTwo] = Racer::factory(2)->for($season)->create(['reliability' => 30]);

    $this->actingAs($user)
        ->post(route('seasons.development.reliability.drivers.store', [$season]), [
            'drivers' => [
                ['id' => $racerOne->id, 'new' => 60],
                ['id' => $racerTwo->id, 'new' => 55],
            ],
        ])
        ->assertRedirect(route('seasons.development.reliability.drivers', [$season]));

    $this->assertEquals(60, $racerOne->fresh()->reliability);
    $this->assertEquals(55, $racerTwo->fresh()->reliability);
});

test('an unauthenticated user cant update driver reliability', function () {
    $season = Season::factory()->create();
    [$racerOne, $racerTwo] = Racer::factory(2)->for($season)->create(['reliability' => 30]);

    $this->post(route('seasons.development.reliability.drivers.store', [$season]), [
        'drivers' => [
            ['id' => $racerOne->id, 'new' => 60],
            ['id' => $racerTwo->id, 'new' => 55],
        ],
    ])
        ->assertForbidden();

    $this->assertEquals(30, $racerOne->fresh()->reliability);
    $this->assertEquals(30, $racerTwo->fresh()->reliability);
});

test('an authenticated user cant update another users driver reliability', function () {
    $season = Season::factory()->creatE();
    [$racerOne, $racerTwo] = Racer::factory(2)->for($season)->create(['reliability' => 30]);

    $this->actingAs(User::factory()->create())
        ->post(route('seasons.development.reliability.drivers.store', [$season]), [
            'drivers' => [
                ['id' => $racerOne->id, 'new' => 60],
                ['id' => $racerTwo->id, 'new' => 55],
            ],
        ])
        ->assertForbidden();

    $this->assertEquals(30, $racerOne->fresh()->reliability);
    $this->assertEquals(30, $racerTwo->fresh()->reliability);
});

test('the driver in a request must exist in the database', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    $this->actingAs($user)
        ->post(route('seasons.development.reliability.drivers.store', [$season]), [
            'drivers' => [
                ['id' => 'invalid id', 'new' => 60],
            ],
        ])
        ->assertSessionHasErrors(['drivers.0.id' => 'The selected drivers.0.id is invalid.']);
});

test('the new reliability must be at least one', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $racer = Racer::factory()->for($season)->create();

    $this->actingAs($user)
        ->post(route('seasons.development.reliability.drivers.store', [$season]), [
            'drivers' => [
                ['id' => $racer->id, 'new' => 0],
            ],
        ])
        ->assertSessionHasErrors(['drivers.0.new' => 'The drivers.0.new must be at least 1.']);
});
