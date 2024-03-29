<?php

use App\Models\Circuit;
use App\Models\Race;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use function Pest\Laravel\assertDatabaseCount;

test('an authenticated user can create a circuit', function () {
    $user = User::factory()->create();
    $this->actingAs($user)
        ->post(route('circuits.store'), [
            'name' => 'Zandvoort',
            'country' => 'nl',
        ])
        ->assertRedirect(route('circuits.index'));

    $this->assertDatabaseCount('circuits', 1);
    $this->assertCount(1, $user->circuits);
});

test('an unauthenticated user can\'t create a circuit', function () {
    $this->post(route('circuits.store'), [
        'name' => 'Zandvoort',
        'country' => 'nl',
    ])
        ->assertForbidden();

    $this->assertDatabaseCount('circuits', 0);
});

test('an authenticated user can update their own circuits', function () {
    $user = User::factory()->create();
    $circuit = Circuit::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->put(route('circuits.update', [$circuit]), [
            'name' => 'New name',
            'country' => 'NC',
        ])->assertRedirect(route('circuits.index'));

    $this->assertEquals('New name', $circuit->fresh()->name);
    $this->assertEquals('NC', $circuit->fresh()->country);
});

test('an authenticated user can\'t update someone else\'s circuits', function () {
    $user = User::factory()->create();
    $circuit = Circuit::factory()->create(['user_id' => $user->id]);
    $name = $circuit->name;
    $country = $circuit->country;

    $this->actingAs(User::factory()->create())
        ->put(route('circuits.update', [$circuit]), [
            'name' => 'New name',
            'country' => 'NC',
        ])->assertForbidden();

    $this->assertEquals($name, $circuit->fresh()->name);
    $this->assertEquals($country, $circuit->fresh()->country);
});

test('an authenticated user can view the circuit create page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('circuits.create'))
        ->assertOk();
});

test('an authenticated user can\'t view the circuit create page', function () {
    $this->get(route('circuits.create'))
        ->assertRedirect(route('index'));
});

test('an authenticated user can view the circuit edit page', function () {
    $user = User::factory()->create();
    $circuit = Circuit::factory()->for($user)->create();

    $this->actingAs($user)
        ->get(route('circuits.edit', [$circuit]))
        ->assertOk();
});

test('an authenticated user can\'t view the circuit edit page', function () {
    $circuit = Circuit::factory()->create();

    $this->get(route('circuits.edit', [$circuit]))
        ->assertRedirect(route('index'));
});

test('an authenticated user can\'t view another user\'s circuit edit page', function () {
    $circuit = Circuit::factory()->create();

    $this->actingAs(User::factory()->create())
        ->get(route('circuits.edit', [$circuit]))
        ->assertForbidden();
});

it('shows all circuits created by a user on the index page', function () {
    $user = User::factory()->create();
    Circuit::factory(5)->for($user)->create();

    $this->actingAs($user)
        ->get(route('circuits.index'))
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Circuits/Index')
                ->has('circuits', 5)
                ->has('filters'),
        );
});

test('an authenticated user can delete circuits', function () {
    $user = User::factory()->create();
    $circuit = Circuit::factory()->for($user)->create();

    $this->actingAs($user)
        ->delete(route('circuits.destroy', $circuit))
        ->assertRedirect();

    assertDatabaseCount('circuits', 0);
});

test('an unauthorised  user cannot delete circuits', function () {
    $circuit = Circuit::factory()->create();

    $this->delete(route('circuits.destroy', $circuit))
        ->assertForbidden();

    assertDatabaseCount('circuits', 1);

    $this->actingAs(User::factory()->create())
        ->delete(route('circuits.destroy', $circuit))
        ->assertForbidden();

    assertDatabaseCount('circuits', 1);
});

test('a circuit cannot be removed once it has been used for a race', function () {
    $this->withoutExceptionHandling();
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $circuit = Circuit::factory()->for($user)->create();

    Race::factory()->for($season)->for($circuit)->create();

    $this->actingAs($user)
        ->delete(route('circuits.destroy', $circuit));

    assertDatabaseCount('circuits', 1);
})->throws(Exception::class);
