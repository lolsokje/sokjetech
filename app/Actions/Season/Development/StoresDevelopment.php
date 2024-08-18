<?php

declare(strict_types=1);

namespace App\Actions\Season\Development;

use App\ValueObjects\Season\Development\DevelopmentEntity;

interface StoresDevelopment
{
    /**
     * @param array<DevelopmentEntity> $entities
     */
    public function handle(
        string $seasonId,
        string $raceId,
        array $entities,
        string $component,
    ): void;
}
