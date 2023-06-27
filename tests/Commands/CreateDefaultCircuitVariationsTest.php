<?php

use App\Models\Circuit;
use App\Models\CircuitVariation;
use App\Support\Formatters\LaptimeFormatter;

it('creates default variations for circuits', function () {
    Circuit::factory(20)->create();

    $this->assertCount(0, CircuitVariation::all());

    Artisan::call('circuits:default-variations');

    $this->assertCount(20, CircuitVariation::all());
});

it('does not create default variations for circuits with existing variations', function () {
    Circuit::factory(20)->create();

    $circuits = Circuit::inRandomOrder()->limit(3)->get();

    foreach ($circuits as $circuit) {
        $circuit->variations()->create([
            'name' => $circuit->name,
            'length' => fake()->numberBetween(4500, 7000),
            'base_laptime' => LaptimeFormatter::toString(fake()->numberBetween(66000, 106000)),
        ]);
    }

    $this->assertCount(3, CircuitVariation::all());

    Artisan::call('circuits:default-variations');

    $this->assertCount(20, CircuitVariation::all());
});
