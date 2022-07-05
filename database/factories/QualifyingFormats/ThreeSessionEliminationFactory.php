<?php

namespace Database\Factories\QualifyingFormats;

use Illuminate\Database\Eloquent\Factories\Factory;

class ThreeSessionEliminationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'q2_driver_count' => 15,
            'q3_driver_count' => 10,
            'runs_per_session' => 2,
            'min_rng' => 0,
            'max_rng' => 30,
        ];
    }
}
