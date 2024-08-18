<?php

declare(strict_types=1);

namespace App\Factories;

use App\Actions\Season\Development\StoreDriverDevelopment;
use App\Actions\Season\Development\StoreEngineDevelopment;
use App\Actions\Season\Development\StoresDevelopment;
use App\Actions\Season\Development\StoreTeamDevelopment;
use App\Enums\Season\Development\DevelopmentType;

final readonly class StoreDevelopmentEntityActionFactory
{
    public static function create(
        DevelopmentType $type,
    ): StoresDevelopment {
        return match ($type) {
            DevelopmentType::DRIVER => new StoreDriverDevelopment,
            DevelopmentType::TEAM => new StoreTeamDevelopment,
            DevelopmentType::ENGINE => new StoreEngineDevelopment,
        };
    }
}
