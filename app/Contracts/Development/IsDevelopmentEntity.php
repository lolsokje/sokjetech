<?php

declare(strict_types=1);

namespace App\Contracts\Development;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * @property Collection developmentHistories
 */
interface IsDevelopmentEntity
{
    public function getLabel(): string;

    public function getStyleString(): string;

    public function getExtra(): array;

    public function developmentHistories(): HasMany;
}
