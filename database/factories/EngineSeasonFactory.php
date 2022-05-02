<?php

namespace Database\Factories;

use App\Models\Engine;
use App\Models\Season;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EngineSeason>
 */
class EngineSeasonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'base_engine_id' => Engine::factory(),
            'season_id' => Season::factory(),
            'name' => $this->faker->name(),
            'rebadge' => false,
            'individual_rating' => false,
            'rating' => $this->faker->numberBetween(40, 100),
            'reliability' => $this->faker->numberBetween(40, 100),
        ];
    }
}
