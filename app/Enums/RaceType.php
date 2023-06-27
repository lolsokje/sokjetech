<?php

namespace App\Enums;

enum RaceType: int
{
    case LAP = 0;
    case TIME = 1;
    case DISTANCE = 2;

    public static function labels(): array
    {
        return [
            self::LAP->value => 'Lap',
            self::TIME->value => 'Time',
            self::DISTANCE->value => 'Distance',
        ];
    }
}
