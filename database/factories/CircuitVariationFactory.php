<?php

namespace Database\Factories;

use App\Models\Circuit;
use App\Models\CircuitVariation;
use App\Support\LaptimeFormatter;
use Illuminate\Database\Eloquent\Factories\Factory;

class CircuitVariationFactory extends Factory
{
    protected $model = CircuitVariation::class;

    public function definition(): array
    {
        return [
            'circuit_id' => Circuit::factory(),
            'name' => $this->faker->name(),
            'length' => $this->faker->numberBetween(4300, 7000),
            'base_laptime' => LaptimeFormatter::toString($this->faker->numberBetween(66500, 106500)),
        ];
    }
}
