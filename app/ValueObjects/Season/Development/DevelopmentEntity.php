<?php

declare(strict_types=1);

namespace App\ValueObjects\Season\Development;

use App\Contracts\Development\HasRatingHistory;
use App\Contracts\Development\IsDevelopmentEntity;
use Illuminate\Support\Collection;

final readonly class DevelopmentEntity
{
    public function __construct(
        public string $id,
        public string $label,
        public int $current,
        public ?string $styleString = null,
        public array $extra = [],
        public int $min = 0,
        public int $max = 0,
        public int $rng = 0,
        public int $new = 0,
    ) {
    }

    public static function fromModel(IsDevelopmentEntity&HasRatingHistory $model, string $component): DevelopmentEntity
    {
        return new self(
            id: $model->getKey(),
            label: $model->getLabel(),
            current: $model->getComponentRating($component),
            styleString: $model->getStyleString(),
            extra: $model->getExtra(),
        );
    }

    /**
     * @return Collection<DevelopmentEntity>
     */
    public static function fromRequest(array $entities): Collection
    {
        return collect($entities)
            ->map(function (array $entity) {
                return new DevelopmentEntity(
                    id: $entity['id'],
                    label: $entity['label'],
                    current: $entity['current'],
                    styleString: $entity['styleString'],
                    extra: $entity['extra'],
                    min: $entity['min'],
                    max: $entity['max'],
                    rng: $entity['rng'],
                    new: $entity['new'],
                );
            });
    }
}
