<?php

namespace Database\Factories;

use App\Enums\SuggestionStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SuggestionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'type' => $this->faker->word(),
            'summary' => $this->faker->text(),
            'details' => $this->faker->word(),
            'status' => SuggestionStatus::NEW->value,
        ];
    }
}
