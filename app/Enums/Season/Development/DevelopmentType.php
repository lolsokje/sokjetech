<?php

declare(strict_types=1);

namespace App\Enums\Season\Development;

enum DevelopmentType: string
{
    case DRIVER = 'driver';
    case TEAM = 'team';
    case ENGINE = 'engine';
}
