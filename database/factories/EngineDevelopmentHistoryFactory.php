<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\EngineDevelopmentHistory;
use App\Models\EngineSeason;
use App\Models\Season;
use Illuminate\Database\Eloquent\Factories\Factory;

final class EngineDevelopmentHistoryFactory extends Factory
{
    protected $model = EngineDevelopmentHistory::class;

    public function definition(): array
    {
        return [
            'season_id' => Season::factory(),
            'engine_season_id' => EngineSeason::factory(),
            'race_id' => $this->faker->randomNumber(),
            'component' => 'rating',
            'initial' => $this->faker->randomNumber(),
            'development' => $this->faker->randomNumber(),
        ];
    }
}
