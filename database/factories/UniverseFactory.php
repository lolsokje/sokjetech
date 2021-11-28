<?php

namespace Database\Factories;

use App\Models\Universe;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UniverseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'visibility' => Universe::VISIBILITY_PUBLIC,
            'user_id' => User::factory()->create()->id,
        ];
    }
}
