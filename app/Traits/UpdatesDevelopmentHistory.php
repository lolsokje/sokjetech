<?php

declare(strict_types=1);

namespace App\Traits;

use App\Contracts\Development\HasDevelopmentHistory;
use App\Contracts\Development\HasRatingHistory;
use App\ValueObjects\Season\Development\DevelopmentEntity;
use Illuminate\Database\Eloquent\Model;

trait UpdatesDevelopmentHistory
{
    public function updateModel(
        DevelopmentEntity $entity,
        string $component,
    ): void {
        /** @var HasRatingHistory|null $model */
        $model = $this->models[$entity->id] ?? null;

        if (! $model) {
            return;
        }

        /** @var Model&HasDevelopmentHistory $history */
        $history = $model->getHistoryTableQuery()
            ->firstOrCreate([
                'season_id' => $this->seasonId,
                $model->getForeignKey() => $model->getKey(),
                'race_id' => $this->raceId,
                'component' => $component,
            ]);

        $update = [
            'development' => $history->getCurrentDevelopment() + $entity->rng,
        ];

        if ($history->wasRecentlyCreated) {
            $update['initial'] = $model->getComponentRating($component);
        }

        $history->update($update);

        $model->update([
            $component => $entity->new,
        ]);
    }
}
