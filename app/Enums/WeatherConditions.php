<?php

namespace App\Enums;

use Illuminate\Support\Collection;

enum WeatherConditions: int
{
    case CLEAR = 0;
    case PARTLY_OVERCAST = 1;
    case OVERCAST = 2;
    case LIGHT_RAIN = 3;
    case RAIN = 4;
    case HEAVY_RAIN = 5;
    case LWBSL = 6;

    public static function casesWithLabels(): Collection
    {
        $cases = collect(self::cases());

        return $cases->mapWithKeys(fn (WeatherConditions $condition) => [$condition->value => $condition->label()]);
    }

    public function label(): string
    {
        return match ($this) {
            self::CLEAR => 'Clear',
            self::PARTLY_OVERCAST => 'Partly overcast',
            self::OVERCAST => 'Overcast',
            self::LIGHT_RAIN => 'Light rain',
            self::RAIN => 'Rain',
            self::HEAVY_RAIN => 'Heavy rain',
            self::LWBSL => 'LWBSL',
        };
    }
}
