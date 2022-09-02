<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CircuitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->city(),
            'country' => $this->faker->countryCode(),
            'user_id' => User::factory(),
            'shared' => false,
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
