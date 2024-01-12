<?php

namespace Database\Factories;

use App\Models\Entrant;
use App\Models\QualifyingResult;
use App\Models\Race;
use App\Models\Racer;
use App\Models\Season;
use Illuminate\Database\Eloquent\Factories\Factory;

class QualifyingResultFactory extends Factory
{
    protected $model = QualifyingResult::class;

    public function definition(): array
    {
        return [
            'position' => $this->faker->randomNumber(),
            'runs' => json_encode([]),
            'race_id' => Race::factory(),
            'season_id' => Season::factory(),
            'racer_id' => Racer::factory(),
            'entrant_id' => Entrant::factory(),
            'driver_rating' => $this->faker->numberBetween(0, 100),
            'team_rating' => $this->faker->numberBetween(0, 100),
            'engine_rating' => $this->faker->numberBetween(0, 100),
        ];
    }
}
