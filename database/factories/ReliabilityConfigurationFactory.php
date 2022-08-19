<?php

namespace Database\Factories;

use App\Models\ReliabilityConfiguration;
use App\Models\Season;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReliabilityConfiguration>
 */
class ReliabilityConfigurationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'season_id' => Season::factory(),
            'min_rng' => $this->faker->numberBetween(1, 100),
            'max_rng' => $this->faker->numberBetween(1, 100),
        ];
    }
}
