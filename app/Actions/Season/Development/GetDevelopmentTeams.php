<?php

declare(strict_types=1);

namespace App\Actions\Season\Development;

use App\Models\Entrant;
use App\Models\Season;
use App\ValueObjects\Season\Development\DevelopmentEntity;

final readonly class GetDevelopmentTeams implements ReturnsDevelopmentEntities
{
    public function handle(
        Season $season,
        string $attribute,
    ): array {
        $teams = $season
            ->entrants()
            ->orderBy('full_name')
            ->get();

        return $teams->map(fn(Entrant $team) => DevelopmentEntity::fromModel($team, $attribute))->toArray();
    }
}
