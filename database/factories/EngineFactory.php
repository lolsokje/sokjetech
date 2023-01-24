<?php

namespace Database\Factories;

use App\Models\Series;
use Illuminate\Database\Eloquent\Factories\Factory;

class EngineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'series_id' => Series::factory(),
            'name' => $this->faker->name(),
        ];
    }

    public function shared(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'shared' => true,
            ];
        });
    }
}
