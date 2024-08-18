<?php

declare(strict_types=1);

namespace App\Contracts\Development;

interface HasDevelopmentHistory
{
    public function getCurrentDevelopment(): ?int;
}
