<?php

namespace Database\Factories;

use App\Enums\DistanceType;
use App\Enums\RaceType;
use App\Models\Circuit;
use App\Models\CircuitVariation;
use App\Models\Season;
use Illuminate\Database\Eloquent\Factories\Factory;

class RaceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'season_id' => Season::factory(),
            'circuit_id' => Circuit::factory(),
            'circuit_variation_id' => CircuitVariation::factory(),
            'name' => "{$this->faker->country()} Grand Prix",
            'order' => 1,
            'race_type' => RaceType::LAP,
            'distance_type' => DistanceType::KM,
            'duration' => 50,
            'qualifying_session' => 1,
            'qualifying_run' => 1,
            'current_lap' => 0,
            'qualifying_started' => false,
            'qualifying_completed' => false,
            'started' => false,
            'completed' => false,
        ];
    }

    public function raceReady(): self
    {
        return $this->state(function () {
            return [
                'qualifying_started' => true,
                'qualifying_completed' => true,
            ];
        });
    }

    public function completed(): self
    {
        return $this->state(function () {
            return [
                'qualifying_started' => true,
                'qualifying_completed' => true,
                'started' => true,
                'completed' => true,
            ];
        });
    }
}
