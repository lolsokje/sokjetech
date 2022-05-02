<?php

namespace App\Enums;

enum UniverseVisibility: int
{
    case PUBLIC = 1;
    case PRIVATE = 2;
    case AUTH = 3;

    public static function labels(): array
    {
        return [
            self::PUBLIC->value => 'Public',
            self::PRIVATE->value => 'Private',
            self::AUTH->value => 'Logged in only',
        ];
    }
}
