<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\DriverDevelopmentHistory;
use App\Models\Racer;
use App\Models\Season;
use Illuminate\Database\Eloquent\Factories\Factory;

final class DriverDevelopmentHistoryFactory extends Factory
{
    protected $model = DriverDevelopmentHistory::class;

    public function definition(): array
    {
        return [
            'season_id' => Season::factory(),
            'racer_id' => Racer::factory(),
            'race_id' => $this->faker->randomNumber(),
            'component' => 'rating',
            'initial' => $this->faker->numberBetween(30, 60),
            'development' => $this->faker->numberBetween(-5, 5),
        ];
    }
}
