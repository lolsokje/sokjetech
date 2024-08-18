<?php

declare(strict_types=1);

namespace App\Factories;

use App\Actions\Season\Development\GetDevelopmentDrivers;
use App\Actions\Season\Development\GetDevelopmentEngines;
use App\Actions\Season\Development\GetDevelopmentTeams;
use App\Actions\Season\Development\ReturnsDevelopmentEntities;
use App\Enums\Season\Development\DevelopmentType;

final readonly class DevelopmentEntityActionFactory
{
    public static function create(
        DevelopmentType $type,
    ): ReturnsDevelopmentEntities {
        return match ($type) {
            DevelopmentType::DRIVER => new GetDevelopmentDrivers,
            DevelopmentType::TEAM => new GetDevelopmentTeams,
            DevelopmentType::ENGINE => new GetDevelopmentEngines,
        };
    }
}
