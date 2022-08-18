<?php

namespace Database\Factories;

use App\Models\Universe;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamFactory extends Factory
{
    public function definition(): array
    {
        return [
            'universe_id' => Universe::factory(),
            'full_name' => $this->faker->userName(),
            'short_name' => $this->faker->firstName(),
            'team_principal' => $this->faker->name(),
            'primary_colour' => $this->faker->hexColor(),
            'secondary_colour' => $this->faker->hexColor(),
            'country' => $this->faker->countryCode(),
        ];
    }
}
