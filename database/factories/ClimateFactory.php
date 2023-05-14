<?php

namespace Database\Factories;

use App\Models\Climate;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClimateFactory extends Factory
{
    protected $model = Climate::class;

    public function definition(): array
    {
        return [
            'short_name' => $this->faker->word(),
            'long_name' => $this->faker->word(),
        ];
    }
}
