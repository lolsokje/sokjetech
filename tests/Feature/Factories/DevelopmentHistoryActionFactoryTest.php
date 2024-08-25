<?php

use App\Actions\Season\Development\History\GetDriverDevelopmentHistory;
use App\Actions\Season\Development\History\GetEngineDevelopmentHistory;
use App\Actions\Season\Development\History\GetTeamDevelopmentHistory;
use App\Enums\Season\Development\DevelopmentType;
use App\Factories\DevelopmentHistoryActionFactory;

it('returns the right action class', function (DevelopmentType $type, string $class) {
    $this->assertInstanceOf($class, DevelopmentHistoryActionFactory::create($type));
})->with([
    [DevelopmentType::DRIVER, GetDriverDevelopmentHistory::class],
    [DevelopmentType::TEAM, GetTeamDevelopmentHistory::class],
    [DevelopmentType::ENGINE, GetEngineDevelopmentHistory::class],
]);
