<?php

declare(strict_types=1);

namespace App\Factories;

use App\Actions\Season\Development\History\GetDriverDevelopmentHistory;
use App\Actions\Season\Development\History\GetEngineDevelopmentHistory;
use App\Actions\Season\Development\History\GetTeamDevelopmentHistory;
use App\Contracts\Development\ReturnsDevelopmentHistory;
use App\Enums\Season\Development\DevelopmentType;

class DevelopmentHistoryActionFactory
{
    public static function create(
        DevelopmentType $type,
    ): ReturnsDevelopmentHistory {
        return match ($type) {
            DevelopmentType::DRIVER => new GetDriverDevelopmentHistory,
            DevelopmentType::TEAM => new GetTeamDevelopmentHistory,
            DevelopmentType::ENGINE => new GetEngineDevelopmentHistory,
        };
    }
}
