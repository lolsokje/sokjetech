<?php

namespace Database\Factories;

use App\Models\Circuit;
use App\Models\Season;
use App\Models\Series;
use Illuminate\Database\Eloquent\Factories\Factory;

class RaceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $series = Series::factory()->create();
        $season = Season::factory()->for($series)->create();
        $user = $series->user;
        return [
            'season_id' => $season,
            'circuit_id' => Circuit::factory()->for($user)->create(),
            'name' => "$season->year {$this->faker->country()} Grand Prix",
            'stints' => [['min_rng' => 0, 'max_rng' => 30]],
            'order' => 1,
            'completed' => false,
        ];
    }
}
