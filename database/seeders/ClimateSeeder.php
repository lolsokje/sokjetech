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
                'name' => 'EU, NA',
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
                'name' => 'SA, AS',
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
                'name' => 'SA, AS (Monsoon)',
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
                'name' => 'OC',
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
                'name' => 'ME, AF',
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
                'name' => $climateDate['name'],
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
