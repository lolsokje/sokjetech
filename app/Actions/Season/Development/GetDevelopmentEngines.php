<?php

declare(strict_types=1);

namespace App\Actions\Season\Development;

use App\Models\EngineSeason;
use App\Models\Season;
use App\ValueObjects\Season\Development\DevelopmentEntity;

final readonly class GetDevelopmentEngines implements ReturnsDevelopmentEntities
{
    public function handle(
        Season $season,
        string $attribute,
    ): array {
        $engines = $season
            ->engines()
            ->orderBy('name')
            ->get();

        return $engines->map(fn(EngineSeason $engine) => DevelopmentEntity::fromModel($engine, $attribute))->toArray();
    }
}
