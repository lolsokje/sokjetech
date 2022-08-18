<?php

namespace Database\Factories;

use App\Models\ReliabilityConfiguration;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReliabilityConfiguration>
 */
class ReliabilityConfigurationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'min_rng' => $this->faker->numberBetween(1, 100),
            'max_rng' => $this->faker->numberBetween(1, 100),
        ];
    }
}
