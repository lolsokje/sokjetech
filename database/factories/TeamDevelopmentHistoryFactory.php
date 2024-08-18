<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Entrant;
use App\Models\Season;
use App\Models\TeamDevelopmentHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

final class TeamDevelopmentHistoryFactory extends Factory
{
    protected $model = TeamDevelopmentHistory::class;

    public function definition(): array
    {
        return [
            'season_id' => Season::factory(),
            'entrant_id' => Entrant::factory(),
            'race_id' => $this->faker->randomNumber(),
            'component' => 'rating',
            'initial' => $this->faker->randomNumber(),
            'development' => $this->faker->randomNumber(),
        ];
    }
}
