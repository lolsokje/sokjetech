<?php

use App\Models\Circuit;
use App\Models\CircuitVariation;
use App\Models\User;
use App\Support\LaptimeFormatter;
use Inertia\Testing\AssertableInertia;

dataset('unauthorised', [
    fn () => User::factory()->create(),
    null,
]);

it('lists all circuit variations on the circuit edit page', function () {
    $user = User::factory()->create();
    $circuit = Circuit::factory()->for($user)->create();
    CircuitVariation::factory()->for($circuit)->create();

    $this->actingAs($user)
        ->get(route('circuits.edit', $circuit))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->has('variations', 1));
});

test('circuit owners can view the variation create page', function () {
    $user = User::factory()->create();
    $circuit = Circuit::factory()->for($user)->create();

    $this->actingAs($user)
        ->get(route('circuits.variations.create', $circuit))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Circuits/Variations/Create')
            ->has('circuit', fn (AssertableInertia $prop) => $prop
                ->where('id', $circuit->id)
                ->etc()));
});

test('circuit owners can add variations to their circuits', function () {
    $user = User::factory()->create();
    $circuit = Circuit::factory()->for($user)->create();

    $laptime = fake()->numberBetween(66000, 106000);

    $this->actingAs($user)
        ->post(route('circuits.variations.store', $circuit), [
            'name' => fake()->name(),
            'length' => fake()->numberBetween(4500, 7000),
            'base_laptime' => LaptimeFormatter::toString($laptime),
        ])
        ->assertRedirectToRoute('circuits.edit', $circuit);

    $this->assertCount(1, CircuitVariation::all());
    $this->assertCount(1, $circuit->variations);
    $this->assertEquals($laptime, CircuitVariation::first()->base_laptime);
});

test('circuit owners can view the circuit variation edit page', function () {
    $user = User::factory()->create();
    $circuit = Circuit::factory()->for($user)->create();
    $variation = CircuitVariation::factory()->for($circuit)->create();

    $this->actingAs($user)
        ->get(route('circuits.variations.edit', [$circuit, $variation]))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Circuits/Variations/Edit')
            ->has('variation', fn (AssertableInertia $prop) => $prop
                ->where('id', $variation->id)
                ->etc()));
});

test('circuit owners can update circuit variations', function () {
    $user = User::factory()->create();
    $circuit = Circuit::factory()->for($user)->create();
    $variation = CircuitVariation::factory()->for($circuit)->create();

    $this->actingAs($user)
        ->from(route('circuits.variations.edit', [$circuit, $variation]))
        ->put(route('circuits.variations.update', [$circuit, $variation]), [
            'name' => 'New',
            'length' => $variation->length,
            'base_laptime' => $variation->readable_laptime,
        ])
        ->assertRedirectToRoute('circuits.variations.edit', [$circuit, $variation]);

    $this->assertEquals('New', $variation->fresh()->name);
});

test('the provided base laptime must be in the correct format', function () {
    $user = User::factory()->create();
    $circuit = Circuit::factory()->for($user)->create();

    $this->actingAs($user)
        ->post(route('circuits.variations.store', $circuit), [
            'name' => fake()->name(),
            'length' => fake()->numberBetween(4500, 7000),
            'base_laptime' => '1:23.456',
        ]);

    $variation = CircuitVariation::first();

    $this->assertEquals(83456, $variation->base_laptime);
});

test('unauthorised users can not view the circuit variation create page', function (?User $user) {
    $circuit = Circuit::factory()->create();

    if ($user) {
        $this->actingAs($user);
    }

    $this->get(route('circuits.variations.create', $circuit))
        ->assertForbidden();
})->with('unauthorised');

test('unauthorised users can not create circuit variations', function (?User $user) {
    $circuit = Circuit::factory()->create();

    if ($user) {
        $this->actingAs($user);
    }

    $this->post(route('circuits.variations.store', $circuit), [
        'name' => fake()->name(),
        'length' => fake()->numberBetween(4500, 7000),
        'base_laptime' => LaptimeFormatter::toString(fake()->numberBetween(66000, 106000)),
    ])
        ->assertForbidden();

    $this->assertCount(0, CircuitVariation::all());
})->with('unauthorised');

test('unauthorised users can not view the circuit variation edit page', function (?User $user) {
    $variation = CircuitVariation::factory()->create();

    if ($user) {
        $this->actingAs($user);
    }

    $this->get(route('circuits.variations.edit', [$variation->circuit, $variation]))
        ->assertForbidden();
})->with('unauthorised');

test('unauthorised users can not update circuit variations', function (?User $user) {
    $variation = CircuitVariation::factory()->create();
    $name = $variation->name;

    if ($user) {
        $this->actingAs($user);
    }

    $this->put(route('circuits.variations.update', [$variation->circuit, $variation]), [
        'name' => 'New name',
        'length' => $variation->length,
        'base_laptime' => LaptimeFormatter::toString($variation->base_laptime),
    ])
        ->assertForbidden();

    $this->assertEquals($name, $variation->fresh()->name);
})->with('unauthorised');

test('the provided rating multipliers must be at least 0 when creating a circuit variation', function () {
    $user = User::factory()->create();
    $circuit = Circuit::factory()->for($user)->create();

    $this->actingAs($user)
        ->from(route('circuits.variations.create', $circuit))
        ->post(route('circuits.variations.store', $circuit), [
            'name' => 'New name',
            'length' => fake()->numberBetween(4500, 7000),
            'base_laptime' => LaptimeFormatter::toString(fake()->numberBetween(66000, 106000)),
            'team_multiplier' => -1,
            'engine_multiplier' => -1,
        ])
        ->assertRedirectToRoute('circuits.variations.create', $circuit)
        ->assertSessionHasErrors([
            'team_multiplier' => 'The team multiplier must be at least 0.',
            'engine_multiplier' => 'The engine multiplier must be at least 0.',
        ]);

    $this->assertCount(0, $circuit->fresh()->variations);
});

test('the provided rating multipliers must be at least 0 when updating a circuit variation', function () {
    $user = User::factory()->create();
    $circuit = Circuit::factory()->for($user)->create();
    $variation = CircuitVariation::factory()->for($circuit)->create();
    $name = $variation->name;

    $this->actingAs($user)
        ->from(route('circuits.variations.edit', [$circuit, $variation]))
        ->put(route('circuits.variations.update', [$circuit, $variation]), [
            'name' => 'New name',
            'length' => fake()->numberBetween(4500, 7000),
            'base_laptime' => LaptimeFormatter::toString(fake()->numberBetween(66000, 106000)),
            'team_multiplier' => -1,
            'engine_multiplier' => -1,
        ])
        ->assertRedirectToRoute('circuits.variations.edit', [$circuit, $variation])
        ->assertSessionHasErrors([
            'team_multiplier' => 'The team multiplier must be at least 0.',
            'engine_multiplier' => 'The engine multiplier must be at least 0.',
        ]);

    $this->assertEquals($name, $variation->fresh()->name);
});

it('sets the rating multipliers to 1 when not provided', function () {
    $user = User::factory()->create();
    $circuit = Circuit::factory()->for($user)->create();

    $this->actingAs($user)
        ->from(route('circuits.variations.create', $circuit))
        ->post(route('circuits.variations.store', $circuit), [
            'name' => 'New name',
            'length' => fake()->numberBetween(4500, 7000),
            'base_laptime' => LaptimeFormatter::toString(fake()->numberBetween(66000, 106000)),
        ]);

    $this->assertCount(1, $circuit->fresh()->variations);

    $variation = $circuit->variations()->first();
    $this->assertEquals(1.0, $variation->team_multiplier);
    $this->assertEquals(1.0, $variation->engine_multiplier);
});
