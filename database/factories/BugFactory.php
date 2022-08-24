<?php

namespace Database\Factories;

use App\Enums\BugStatus;
use App\Models\Bug;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BugFactory extends Factory
{
    protected $model = Bug::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => $this->faker->word(),
            'summary' => $this->faker->sentence(),
            'details' => $this->faker->realText(),
            'status' => BugStatus::NEW,
        ];
    }

    public function random(): static
    {
        return $this->state(function ($attributes) {
            return [
                'status' => $this->faker->randomElement(BugStatus::cases()),
            ];
        });
    }
}
