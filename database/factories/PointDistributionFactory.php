<?php

namespace Database\Factories;

use App\Models\PointSystem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PointDistribution>
 */
class PointDistributionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'point_system_id' => PointSystem::factory(),
            'position' => $this->faker->numberBetween(1, 20),
            'points' => $this->faker->numberBetween(1, 25),
        ];
    }
}
