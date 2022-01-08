<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'discord_id' => $this->faker->randomNumber(9),
            'username' => $this->faker->userName(),
            'avatar' => null,
            'is_admin' => false,
        ];
    }
}
