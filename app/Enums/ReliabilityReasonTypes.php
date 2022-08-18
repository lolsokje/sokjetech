<?php

namespace App\Enums;

enum ReliabilityReasonTypes: int
{
    case ENGINE = 1;
    case TEAM = 2;
    case DRIVER = 3;

    public static function keyNames(): array
    {
        return [
            'engine',
            'team',
            'driver',
        ];
    }
}
