<?php

namespace Database\Factories;

use App\Models\Entrant;
use App\Models\Race;
use App\Models\Racer;
use App\Models\RaceResult;
use App\Models\Season;
use Illuminate\Database\Eloquent\Factories\Factory;

class RaceResultFactory extends Factory
{
    protected $model = RaceResult::class;

    public function definition(): array
    {
        return [
            'starting_position' => $this->faker->randomNumber(),
            'position' => $this->faker->randomNumber(),
            'fastest_lap' => $this->faker->boolean(),
            'points' => $this->faker->randomNumber(),
            'race_id' => Race::factory(),
            'season_id' => Season::factory(),
            'racer_id' => Racer::factory(),
            'entrant_id' => Entrant::factory(),
        ];
    }
}
