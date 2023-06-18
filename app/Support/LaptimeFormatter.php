<?php

namespace App\Support;

use InvalidArgumentException;

class LaptimeFormatter
{
    public static function toString(int $laptime): string
    {
        $minutes = (int) ($laptime / 60000) % 60;
        $seconds = str_pad((int) ($laptime / 1000) % 60, 2, '0', STR_PAD_LEFT);
        $millis = str_pad($laptime % 1000, 3, '0', STR_PAD_LEFT);

        return "$minutes:$seconds.$millis";
    }

    public static function toInteger(string $laptime): int
    {
        if (! self::validateLaptime($laptime)) {
            throw new InvalidArgumentException('The provided lap time must follow the format [mm:ss.xxx]');
        }

        [$minutesAndSeconds, $millis] = explode('.', $laptime);
        [$minutes, $seconds] = explode(':', $minutesAndSeconds);

        return ((int) $minutes * 60000) + ((int) $seconds * 1000) + (int) $millis;
    }

    public static function validateLaptime(string $laptime): bool
    {
        return preg_match('/\d{1,2}:\d{2}\.\d{3}/', $laptime);
    }
}
