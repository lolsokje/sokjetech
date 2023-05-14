<?php

namespace Database\Seeders;

use App\Enums\WeatherConditions;
use App\Models\Climate;
use Illuminate\Database\Seeder;

class ClimateSeeder extends Seeder
{
    public function run(): void
    {
        $clear = WeatherConditions::CLEAR->value;
        $partlyOvercast = WeatherConditions::PARTLY_OVERCAST->value;
        $overcast = WeatherConditions::OVERCAST->value;
        $lightRain = WeatherConditions::LIGHT_RAIN->value;
        $rain = WeatherConditions::RAIN->value;
        $heavyRain = WeatherConditions::HEAVY_RAIN->value;
        $lwbsl = WeatherConditions::LWBSL->value;

        $climates = [
            [
                'short_name' => 'EU, NA',
                'long_name' => 'Europe, North America',
                'conditions' => [
                    $clear => 30,
                    $partlyOvercast => 20,
                    $overcast => 15,
                    $lightRain => 5,
                    $rain => 10,
                    $heavyRain => 10,
                    $lwbsl => 10,
                ],
            ],
            [
                'short_name' => 'SA, AS',
                'long_name' => 'South America, Asia',
                'conditions' => [
                    $clear => 30,
                    $partlyOvercast => 20,
                    $overcast => 10,
                    $lightRain => 10,
                    $rain => 5,
                    $heavyRain => 10,
                    $lwbsl => 15,
                ],
            ],
            [
                'short_name' => 'SA, AS (Monsoon)',
                'long_name' => 'South America, Asia (Monsoon)',
                'conditions' => [
                    $clear => 15,
                    $partlyOvercast => 15,
                    $overcast => 15,
                    $lightRain => 5,
                    $rain => 10,
                    $heavyRain => 20,
                    $lwbsl => 20,
                ],
            ],
            [
                'short_name' => 'OC',
                'long_name' => 'Oceania',
                'conditions' => [
                    $clear => 40,
                    $partlyOvercast => 15,
                    $overcast => 15,
                    $rain => 10,
                    $heavyRain => 10,
                    $lwbsl => 10,
                ],
            ],
            [
                'short_name' => 'ME, AF',
                'long_name' => 'Middle East, Africa',
                'conditions' => [
                    $clear => 50,
                    $partlyOvercast => 20,
                    $overcast => 10,
                    $rain => 10,
                    $heavyRain => 10,
                ],
            ],
        ];

        foreach ($climates as $climateDate) {
            $climate = Climate::query()->create([
                'short_name' => $climateDate['short_name'],
                'long_name' => $climateDate['long_name'],
            ]);

            foreach ($climateDate['conditions'] as $condition => $chance) {
                $climate->conditions()->create([
                    'condition' => $condition,
                    'chance' => $chance,
                ]);
            }
        }
    }
}
