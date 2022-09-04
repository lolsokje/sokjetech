<?php

namespace Database\Factories;

use App\Models\Universe;
use Illuminate\Database\Eloquent\Factories\Factory;

class DriverFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'universe_id' => Universe::factory(),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'dob' => $this->faker->date(),
            'country' => $this->faker->countryCode(),
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
