<?php

use App\Models\Universe;
use App\Models\User;
use App\Support\DriverGenerator\Locales;
use Inertia\Testing\AssertableInertia as Assert;

test('universe owners can view the driver generation page', function () {
    $user = User::factory()->create();
    $universe = Universe::factory()->for($user)->create();

    $this->actingAs($user)
        ->get(route('universes.drivers.generate.show', $universe))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Drivers/Generate')
            ->has('universe', fn (Assert $prop) => $prop
                ->where('id', $universe->id)
                ->etc())
            ->has('languages', count(Locales::getLanguages())));
});

test('unauthorised users cannot view the driver generation page', function () {
    $universe = Universe::factory()->create();

    $this->get(route('universes.drivers.generate.show', $universe))
        ->assertForbidden();

    $this->actingAs(User::factory()->create())
        ->get(route('universes.drivers.generate.show', $universe))
        ->assertForbidden();
});

test('universe owners can generate random drivers', function () {
    $user = User::factory()->create();
    $universe = Universe::factory()->for($user)->create();

    $this->actingAs($user)
        ->post(route('universes.drivers.generate', $universe), [
            'start' => '1987-01-01',
            'end' => '1999-12-31',
            'amount' => 10,
            'language' => null,
            'gender' => null,
        ])
        ->assertOk()
        ->assertJsonCount(10);
});

test('unauthorised users cannot generate random drivers', function () {
    $universe = Universe::factory()->create();

    $this->post(route('universes.drivers.generate', $universe))
        ->assertForbidden();

    $this->actingAs(User::factory()->create())
        ->post(route('universes.drivers.generate', $universe))
        ->assertForbidden();
});

test('universe owners can persist generated drivers', function () {
    $user = User::factory()->create();
    $universe = Universe::factory()->for($user)->create();

    $drivers = $this->actingAs($user)
        ->post(route('universes.drivers.generate', $universe), [
            'start' => '1987-01-01',
            'end' => '1999-12-31',
            'amount' => 10,
            'language' => null,
            'gender' => null,
        ])
        ->getOriginalContent();

    $this->post(route('universes.drivers.persist', $universe), [
        'drivers' => $drivers,
    ])
        ->assertRedirect(route('universes.drivers.generate.show', $universe));

    $this->assertDatabaseCount('drivers', 10);

    foreach ($drivers as $driver) {
        $this->assertDatabaseHas('drivers', [
            'first_name' => $driver['first_name'],
            'last_name' => $driver['last_name'],
            'country' => $driver['country'],
            'universe_id' => $universe->id,
        ]);
    }
});

test('unauthorised users cannot persist drivers', function () {
    $universe = Universe::factory()->create();

    $this->post(route('universes.drivers.persist', $universe))
        ->assertForbidden();

    $this->actingAs(User::factory()->create())
        ->post(route('universes.drivers.persist', $universe))
        ->assertForbidden();
});
