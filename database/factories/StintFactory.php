<?php

namespace Database\Factories;

use App\Models\Race;
use Illuminate\Database\Eloquent\Factories\Factory;

class StintFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'race_id' => Race::factory(),
            'order' => 1,
            'min_rng' => 0,
            'max_rng' => 30,
            'reliability' => false,
            'use_team_rating' => false,
            'use_driver_rating' => false,
            'use_engine_rating' => false,
        ];
    }
}
