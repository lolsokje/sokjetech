<?php

namespace Database\Factories;

use App\Enums\UniverseVisibility;
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
            'visibility' => UniverseVisibility::PUBLIC,
            'user_id' => User::factory()->create()->id,
        ];
    }

    public function private(): static
    {
        return $this->state(function ($attributes) {
            return [
                'visibility' => UniverseVisibility::PRIVATE,
            ];
        });
    }

    public function auth(): static
    {
        return $this->state(function ($attributes) {
            return [
                'visibility' => UniverseVisibility::AUTH,
            ];
        });
    }
}
