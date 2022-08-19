<?php

namespace App\Enums;

use http\Exception\InvalidArgumentException;

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

    public static function fromString(string $type): int
    {
        return match ($type) {
            'engine' => self::ENGINE->value,
            'team' => self::TEAM->value,
            'driver' => self::DRIVER->value,
            default => throw new InvalidArgumentException("Incorrect type supplied: $type"),
        };
    }
}
