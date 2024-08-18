<?php

declare(strict_types=1);

namespace App\Actions\Season\Development;

use App\ValueObjects\Season\Development\DevelopmentEntity;
use Illuminate\Support\Collection;

interface StoresDevelopment
{
    /**
     * @param Collection<DevelopmentEntity> $entities
     */
    public function handle(
        string $seasonId,
        string $raceId,
        Collection $entities,
        string $component,
    ): void;
}
