<?php

declare(strict_types=1);

namespace App\Actions\Season\Development;

use App\Models\Season;
use App\ValueObjects\Season\Development\DevelopmentEntity;

interface ReturnsDevelopmentEntities
{
    /**
     * @return array<DevelopmentEntity>
     */
    public function handle(
        Season $season,
        string $attribute,
    ): array;
}
