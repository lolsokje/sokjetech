<?php

use App\Actions\Season\Development\StoreDriverDevelopment;
use App\Actions\Season\Development\StoreEngineDevelopment;
use App\Actions\Season\Development\StoreTeamDevelopment;
use App\Enums\Season\Development\DevelopmentType;
use App\Factories\StoreDevelopmentEntityActionFactory;

it('returns the right action class', function (DevelopmentType $type, string $class) {
    $this->assertInstanceOf($class, StoreDevelopmentEntityActionFactory::create($type));
})->with([
    [DevelopmentType::DRIVER, StoreDriverDevelopment::class],
    [DevelopmentType::TEAM, StoreTeamDevelopment::class],
    [DevelopmentType::ENGINE, StoreEngineDevelopment::class],
]);
