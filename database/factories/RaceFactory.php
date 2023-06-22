<?php

namespace Database\Factories;

use App\Models\Circuit;
use App\Models\CircuitVariation;
use App\Models\Season;
use Illuminate\Database\Eloquent\Factories\Factory;

class RaceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'season_id' => Season::factory(),
            'circuit_id' => Circuit::factory(),
            'circuit_variation_id' => CircuitVariation::factory(),
            'name' => "{$this->faker->country()} Grand Prix",
            'order' => 1,
            'qualifying_started' => false,
            'qualifying_completed' => false,
            'started' => false,
            'completed' => false,
        ];
    }
}
