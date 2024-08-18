<?php

use App\Enums\Season\Development\DevelopmentType;
use App\Models\EngineSeason;
use App\Models\Entrant;
use App\Models\Racer;

dataset('driver components', [
    ['rating'],
    ['reliability'],
    // TODO attack and defence
]);

dataset('team components', [
    ['rating'],
    ['reliability'],
    // TODO specific car components
]);

dataset('engine components', [
    ['rating'],
    ['reliability'],
]);

dataset('development options', [
    [DevelopmentType::DRIVER, 'rating', fn() => Racer::factory(2)],
    [DevelopmentType::DRIVER, 'reliability', fn() => Racer::factory(2)],
    [DevelopmentType::ENGINE, 'rating', fn() => EngineSeason::factory(2)],
    [DevelopmentType::ENGINE, 'reliability', fn() => EngineSeason::factory(2)],
    [DevelopmentType::TEAM, 'rating', fn() => Entrant::factory(2)],
    [DevelopmentType::TEAM, 'reliability', fn() => Entrant::factory(2)],
]);
