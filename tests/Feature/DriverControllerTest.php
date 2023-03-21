<?php

use App\Enums\UniverseVisibility;
use App\Models\Driver;
use App\Models\Universe;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('a universe owner can create drivers', function () {
    $user = User::factory()->create();
    $universe = Universe::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->post(route('universes.drivers.store', [$universe]), [
            'first_name' => 'First',
            'last_name' => 'Last',
            'dob' => null,
            'country' => 'AF',
        ])
        ->assertRedirect(route('universes.drivers.index', [$universe]));

    $this->assertDatabaseCount('drivers', 1);
    $this->assertCount(1, $universe->drivers);
});

test('unauthenticated users can\'t create drivers', function () {
    $universe = Universe::factory()->create();

    $this->post(route('universes.drivers.store', [$universe]), [
        'first_name' => 'First',
        'last_name' => 'Last',
        'dob' => now()->format('Y-m-d'),
        'country' => 'AF',
    ])
        ->assertForbidden();

    $this->assertDatabaseCount('drivers', 0);
    $this->assertCount(0, $universe->drivers);
});

test('an authenticated user can\'t create drivers in other universes', function () {
    $universe = Universe::factory()->create();
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('universes.drivers.store', [$universe]), [
            'first_name' => 'First',
            'last_name' => 'Last',
            'dob' => now()->format('Y-m-d'),
            'country' => 'AF',
        ])
        ->assertForbidden();

    $this->assertDatabaseCount('drivers', 0);
    $this->assertCount(0, $universe->drivers);
});

test('a universe owner can edit drivers', function () {
    $user = User::factory()->create();
    $universe = Universe::factory()->create(['user_id' => $user->id]);
    $driver = Driver::factory()->create(['universe_id' => $universe->id]);
    $newDate = now()->subMonth()->format('Y-m-d');

    $this->actingAs($user)
        ->put(route('universes.drivers.update', [$universe, $driver]), [
            'first_name' => 'New First',
            'last_name' => 'New Last',
            'dob' => $newDate,
            'country' => 'AL',
        ])
        ->assertRedirect(route('universes.drivers.index', [$universe]));

    $this->assertEquals('New First', $driver->fresh()->first_name);
    $this->assertEquals('New Last', $driver->fresh()->last_name);
    $this->assertEquals($newDate, $driver->fresh()->dob->format('Y-m-d'));
    $this->assertEquals('AL', $driver->fresh()->country);
});

test('a driver can be marked as retired', function () {
    $user = User::factory()->create();
    $universe = Universe::factory()->create(['user_id' => $user->id]);
    $driver = Driver::factory()->create(['universe_id' => $universe->id]);

    $this->actingAs($user)
        ->put(route('universes.drivers.update', [$universe, $driver]), [
            'first_name' => $driver->first_name,
            'last_name' => $driver->last_name,
            'dob' => $driver->dob,
            'country' => $driver->country,
            'retired' => true,
        ])
        ->assertRedirect(route('universes.drivers.index', $universe));

    $this->assertTrue($driver->fresh()->retired);
});

test('an authenticated user can\'t update drivers', function () {
    $universe = Universe::factory()->create();
    $driver = Driver::factory()->create(['universe_id' => $universe->id]);

    $newDate = now()->subMonth()->format('Y-m-d');

    $this->put(route('universes.drivers.update', [$universe, $driver]), [
        'first_name' => 'New First',
        'last_name' => 'New Last',
        'dob' => $newDate,
        'country' => 'AL',
    ])
        ->assertForbidden();

    $this->assertEquals($driver->first_name, $driver->fresh()->first_name);
    $this->assertEquals($driver->last_name, $driver->fresh()->last_name);
    $this->assertEquals($driver->dob, $driver->fresh()->dob);
    $this->assertEquals($driver->country, $driver->fresh()->country);
});

test('an authenticated user can\'t edit drivers in other universes', function () {
    $universe = Universe::factory()->create();
    $user = User::factory()->create();
    $driver = Driver::factory()->create(['universe_id' => $universe->id]);
    $newDate = now()->subMonth()->format('Y-m-d');

    $this->actingAs($user)
        ->put(route('universes.drivers.update', [$universe, $driver]), [
            'first_name' => 'New First',
            'last_name' => 'New Last',
            'dob' => $newDate,
            'country' => 'AL',
        ])
        ->assertForbidden();

    $this->assertEquals($driver->first_name, $driver->fresh()->first_name);
    $this->assertEquals($driver->last_name, $driver->fresh()->last_name);
    $this->assertEquals($driver->dob, $driver->fresh()->dob);
    $this->assertEquals($driver->country, $driver->fresh()->country);
});

test('an authenticated user can\'t view the driver create page', function () {
    $this->get(route('universes.drivers.create', [Universe::factory()->create()]))
        ->assertRedirect(route('index'));
});

test('an unauthenticated user can\'t view the driver edit page', function () {
    $universe = Universe::factory()->create();
    $driver = Driver::factory()->create(['universe_id' => $universe->id]);
    $this->get(route('universes.drivers.edit', [$universe, $driver]))
        ->assertRedirect(route('index'));
});

test('an authenticated user can view the driver create page', function () {
    $universe = Universe::factory()->create();

    $this->actingAs($universe->user)
        ->get(route('universes.drivers.create', [$universe]))
        ->assertOk();
});

test('an authenticated user can view the driver edit page', function () {
    $universe = Universe::factory()->create();
    $driver = Driver::factory()->for($universe)->create();

    $this->actingAs($universe->user)
        ->get(route('universes.drivers.edit', [$universe, $driver]))
        ->assertOk();
});

it('shows all drivers in the selected universe on the index page', function () {
    $universe = Universe::factory()->create();
    Driver::factory(5)->for($universe)->create();
    Driver::factory(5)->for(Universe::factory()->create())->create();

    $this->actingAs($universe->user)
        ->get(route('universes.drivers.index', [$universe]))
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Drivers/Index')
                ->has('drivers', 5),
        );
});

it('shows the driver detail page for authorised users', function () {
    $user = User::factory()->create();
    $universe = Universe::factory()->for($user)->create();
    $driver = Driver::factory()->for($universe)->create();

    $this->actingAs($user)
        ->get(route('universes.drivers.show', [$universe, $driver]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Drivers/Show')
            ->has('driver', fn (Assert $prop) => $prop
                ->where('id', $driver->id)
                ->etc()));
});

it('does not show the driver detail page for unauthorised users', function () {
    $universe = Universe::factory()->create(['visibility' => UniverseVisibility::PRIVATE]);
    $driver = Driver::factory()->for($universe)->create();

    $this->get(route('universes.drivers.show', [$universe, $driver]))
        ->assertForbidden();

    $this->actingAs(User::factory()->create())
        ->get(route('universes.drivers.show', [$universe, $driver]))
        ->assertForbidden();
});
