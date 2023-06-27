<?php

use App\Models\Circuit;
use App\Models\CircuitVariation;
use App\Models\Climate;
use App\Models\User;
use App\Support\Formatters\LaptimeFormatter;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\assertDatabaseCount;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertFalse;

test('an authenticated user can share circuits with others', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('circuits.store'), [
            'name' => 'Test',
            'country' => 'nl',
            'default_climate_id' => Climate::factory()->create()->id,
            'shared' => true,
            'length' => fake()->numberBetween(4500, 7000),
            'base_laptime' => LaptimeFormatter::toString(fake()->numberBetween(66000, 106000)),
        ])
        ->assertRedirect();

    assertDatabaseCount('circuits', 1);
    assertCount(1, Circuit::query()->shared()->get());
});

test('an authenticated user can unshare circuits', function () {
    $user = User::factory()->create();
    $circuit = Circuit::factory()->for($user)->shared()->create();

    $this->actingAs($user)
        ->put(route('circuits.update', $circuit), [
            'name' => $circuit->name,
            'country' => $circuit->country,
            'default_climate_id' => Climate::factory()->create()->id,
            'shared' => false,
        ])
        ->assertRedirect();

    assertFalse($circuit->fresh()->shared);
    assertCount(0, Circuit::query()->shared()->get());
});

test('unauthenticated users cannot view the circuit database index page', function () {
    $this->get(route('database.circuits.index'))
        ->assertRedirect(route('index'));
});

it('only shows shared circuits on the circuit database index page', function () {
    Circuit::factory(10)->sequence(
        ['shared' => false],
        ['shared' => true],
    )->create();

    $this->actingAs(User::factory()->create())
        ->get(route('database.circuits.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $assert) => $assert
            ->component('Database/Circuits/Index')
            ->has('circuits.data', 5, fn (Assert $prop) => $prop
                ->where('shared', true)
                ->etc()),
        );
});

it('groups circuits by name, country and user', function () {
    $user = User::factory()->create();
    Circuit::factory(2)->shared()->create([
        'name' => 'Test',
        'country' => 'NL',
        'user_id' => $user->id,
    ]);
    Circuit::factory()->shared()->create();

    $this->actingAs($user)
        ->get(route('database.circuits.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $assert) => $assert
            ->component('Database/Circuits/Index')
            ->has('circuits.data', 2));
});

test('an authenticated user can copy a circuit', function () {
    $circuit = Circuit::factory()->shared()->create();
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('database.circuits.copy', $circuit))
        ->assertRedirect(route('database.circuits.index'));

    assertDatabaseCount('circuits', 2);
    assertCount(1, $user->circuits()->get());
});

it('copies selected circuit variations', function () {
    $circuit = Circuit::factory()->shared()->create();
    [$v1, $v2, $v3] = CircuitVariation::factory(3)->for($circuit)->create();
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('database.circuits.copy', $circuit), [
            'variations' => [
                $v1->id,
                $v3->id,
            ],
        ])
        ->assertRedirectToRoute('database.circuits.index');

    $circuit = $user->circuits()->first();

    $this->assertCount(5, CircuitVariation::all());
    $this->assertCount(2, $circuit->variations);

    $newVariations = CircuitVariation::query()
        ->orderBy('id', 'desc')
        ->limit(2)
        ->get();

    foreach ($newVariations as $variation) {
        $this->assertNotNull($circuit->variations()->find($variation->id));
    }
});

it('does not copy circuit variations if none have been provided', function () {
    $circuit = Circuit::factory()->shared()->create();
    CircuitVariation::factory(3)->for($circuit)->create();
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('database.circuits.copy', $circuit))
        ->assertRedirectToRoute('database.circuits.index');

    $this->assertCount(3, CircuitVariation::all());
    $this->assertCount(0, $user->circuits()->first()->variations);
});

test('an unauthenticated user cannot copy a circuit', function () {
    $circuit = Circuit::factory()->shared()->create();

    $this->post(route('database.circuits.copy', $circuit))
        ->assertRedirect(route('index'));

    assertDatabaseCount('circuits', 1);
});

it('paginates shared circuits', function () {
    Circuit::factory(40)->shared()->create();

    $this->actingAs(User::factory()->create())
        ->get(route('database.circuits.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $assert) => $assert
            ->component('Database/Circuits/Index')
            ->has('circuits.data', 20)
            ->has('links', 4));
});
