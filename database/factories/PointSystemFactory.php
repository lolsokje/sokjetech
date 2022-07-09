<?php

namespace Database\Factories;

use App\Models\Season;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PointSystem>
 */
class PointSystemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'season_id' => Season::factory(),
            'fastest_lap_point_awarded' => false,
            'pole_position_point_awarded' => false,
            'fastest_lap_point_amount' => null,
            'pole_position_point_amount' => null,
            'fastest_lap_determination' => null,
            'fastest_lap_min_rng' => null,
            'fastest_lap_max_rng' => null,
        ];
    }
}
