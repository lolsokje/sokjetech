<?php

namespace Database\Factories;

use App\Models\Series;
use Illuminate\Database\Eloquent\Factories\Factory;

class SeasonFactory extends Factory
{
    public function definition(): array
    {
        return [
            'series_id' => Series::factory(),
            'year' => $this->faker->numberBetween(1950, date('Y')),
            'name' => "season",
        ];
    }

    public function started(): self
    {
        return $this->state(function () {
            return [
                'started' => true,
            ];
        });
    }
}
