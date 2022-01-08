<?php

namespace Database\Factories;

use App\Models\Driver;
use App\Models\Entrant;
use App\Models\Season;
use Illuminate\Database\Eloquent\Factories\Factory;

class LineupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'season_id' => Season::factory()->create(),
            'driver_id' => Driver::factory()->create(),
            'entrant_id' => Entrant::factory()->create(),
            'number' => $this->faker->numberBetween(2, 999),
            'rating' => $this->faker->numberBetween(20, 40),
            'reliability' => $this->faker->numberBetween(90, 100),
            'active' => true,
        ];
    }

    public function inactive(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'active' => false,
            ];
        });
    }
}
