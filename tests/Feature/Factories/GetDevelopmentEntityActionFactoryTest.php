<?php

use App\Actions\Season\Development\GetDevelopmentDrivers;
use App\Actions\Season\Development\GetDevelopmentEngines;
use App\Actions\Season\Development\GetDevelopmentTeams;
use App\Enums\Season\Development\DevelopmentType;
use App\Factories\DevelopmentEntityActionFactory;

it('returns the right action class', function (DevelopmentType $type, string $class) {
    $this->assertInstanceOf($class, DevelopmentEntityActionFactory::create($type));
})->with([
    [DevelopmentType::DRIVER, fn() => GetDevelopmentDrivers::class],
    [DevelopmentType::TEAM, fn() => GetDevelopmentTeams::class],
    [DevelopmentType::ENGINE, fn() => GetDevelopmentEngines::class],
]);
