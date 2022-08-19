<?php

namespace Database\Factories;

use App\Enums\ReliabilityReasonTypes;
use App\Models\ReliabilityReason;
use App\Models\Season;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReliabilityReason>
 */
class ReliabilityReasonFactory extends Factory
{
    public function definition(): array
    {
        return [
            'season_id' => Season::factory(),
            'type' => $this->faker->randomElement(ReliabilityReasonTypes::cases()),
            'reason' => $this->faker->word(),
        ];
    }
}
