<?php

namespace App\Enums;

enum SuggestionStatus: string
{
    case NEW = 'new';
    case WONT_IMPLEMENT = 'won\'t implement';
    case WILL_IMPLEMENT = 'will implement';
    case IMPLEMENTED_ON_STAGING = 'implemented on staging';
    case IMPLEMENTED = 'implemented';

    public static function openCases(): array
    {
        return [
            self::NEW,
            self::WILL_IMPLEMENT,
            self::IMPLEMENTED_ON_STAGING
        ];
    }

    public static function closedCases(): array
    {
        return [
            self::WONT_IMPLEMENT,
            self::IMPLEMENTED
        ];
    }
}
