<?php

use App\Models\Circuit;
use App\Models\CircuitVariation;
use App\Models\User;

test('authenticated users can fetch variations for shared circuits', function () {
    [$c1, $c2] = Circuit::factory(2)->create(['name' => 'Circuit']);
    $c3 = Circuit::factory()->create(['name' => 'Different circuit']);

    CircuitVariation::factory()->for($c1)->create();
    CircuitVariation::factory(2)->for($c2)->create();
    CircuitVariation::factory()->for($c3)->create();

    $this->actingAs(User::factory()->create())
        ->get(
            route('database.circuits.variations.index', [
                'circuit' => 'Circuit',
            ]),
        )
        ->assertJsonCount(3, 'data');

    $this->get(
        route('database.circuits.variations.index', [
            'circuit' => 'Different circuit',
        ]),
    )
        ->assertJsonCount(1, 'data');
});
