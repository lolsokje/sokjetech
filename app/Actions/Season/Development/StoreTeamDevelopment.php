<?php

declare(strict_types=1);

namespace App\Actions\Season\Development;

use App\Contracts\Development\HasRatingHistory;
use App\Models\Entrant;
use App\Traits\UpdatesDevelopmentHistory;
use App\ValueObjects\Season\Development\DevelopmentEntity;
use Illuminate\Support\Collection;

final class StoreTeamDevelopment implements StoresDevelopment
{
    use UpdatesDevelopmentHistory;

    private string $seasonId;

    private string $raceId;

    /** @var Collection<string, HasRatingHistory> */
    private Collection $models;

    public function handle(
        string $seasonId,
        string $raceId,
        array $entities,
        string $component,
    ): void {
        $this->seasonId = $seasonId;
        $this->raceId = $raceId;

        $entities = DevelopmentEntity::fromRequest($entities);

        $this->models = $this->getTeams($entities);

        $entities->each(fn(DevelopmentEntity $entity) => $this->updateModel($entity, $component));
    }

    /**
     * @param Collection<DevelopmentEntity> $entities
     * @return Collection<string, Entrant>
     */
    private function getTeams(Collection $entities): Collection
    {
        $ids = $entities->map(fn(DevelopmentEntity $entity) => $entity->id);

        return Entrant::query()
            ->whereIn('id', $ids)
            ->get()
            ->mapWithKeys(fn(Entrant $team) => [$team->id => $team]);
    }
}
