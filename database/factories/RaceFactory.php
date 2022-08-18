<?php

namespace Database\Factories;

use App\Models\Circuit;
use App\Models\Race;
use App\Models\Season;
use App\Models\Stint;
use Illuminate\Database\Eloquent\Factories\Factory;

class RaceFactory extends Factory
{
    public function configure()
    {
        return $this->afterCreating(fn (Race $race) => Stint::factory(3)->for($race)->create());
    }

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'season_id' => Season::factory(),
            'circuit_id' => Circuit::factory(),
            'name' => "{$this->faker->country()} Grand Prix",
            'order' => 1,
            'qualifying_started' => false,
            'qualifying_completed' => false,
            'started' => false,
            'completed' => false,
        ];
    }
}
