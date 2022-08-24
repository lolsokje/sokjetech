<?php

namespace App\Enums;

enum BugStatus: string
{
    case NEW = 'new';
    case INCOMPLETE = 'incomplete';
    case CONFIRMED = 'confirmed';
    case IN_PROGRESS = 'fix in progress';
    case IN_REVIEW = 'in review';
    case FIXED_ON_STAGING = 'fixed on staging';
    case FIX_RELEASED = 'fix released';
    case WONT_FIX = 'won\'t fix';

    public static function openCases(): array
    {
        return [
            self::NEW,
            self::INCOMPLETE,
            self::CONFIRMED,
            self::IN_PROGRESS,
            self::IN_REVIEW,
            self::FIXED_ON_STAGING,
        ];
    }

    public static function closedCases(): array
    {
        return [
            self::FIX_RELEASED,
            self::WONT_FIX,
        ];
    }
}
