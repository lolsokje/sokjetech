<?php

declare(strict_types=1);

namespace App\Contracts\Development;

interface HasDevelopmentHistory
{
    public function raceId(): string|int;

    public function getInitialRating(): ?int;

    public function getCurrentDevelopment(): ?int;
}
