<?php

namespace Database\Factories;

use App\Models\EngineSeason;
use App\Models\Season;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

class EntrantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $team = Team::factory()->create();
        return [
            'season_id' => Season::factory(),
            'team_id' => $team->id,
            'engine_id' => EngineSeason::factory(),
            'full_name' => $team->full_name,
            'short_name' => $team->short_name,
            'team_principal' => $team->team_principal,
            'primary_colour' => $team->primary_colour,
            'secondary_colour' => $team->secondary_colour,
            'accent_colour' => $team->accent_colour,
            'country' => $team->country,
            'active' => true,
            'rating' => $this->faker->numberBetween(20, 40),
            'reliability' => $this->faker->numberBetween(0, 11),
        ];
    }
}
