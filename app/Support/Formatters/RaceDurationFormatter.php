<?php

namespace App\Support\Formatters;

class RaceDurationFormatter
{
    public static function toHoursAndMinutes(int $duration): array
    {
        $hours = ($duration / 60) % 60;
        $minutes = $duration % 60;

        return [$hours, $minutes];
    }

    public static function metersToMiles(int $meters): int
    {
        return round($meters * 0.00062137);
    }

    public static function metersToKilometers(int $meters): int
    {
        return round($meters / 1000);
    }
}
