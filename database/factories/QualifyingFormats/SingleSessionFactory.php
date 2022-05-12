<?php

namespace Database\Factories\QualifyingFormats;

use Illuminate\Database\Eloquent\Factories\Factory;

class SingleSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'runs_per_session' => 2,
            'min_rng' => 5,
            'max_rng' => 60,
        ];
    }
}
