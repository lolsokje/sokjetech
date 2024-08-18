<?php

declare(strict_types=1);

namespace App\Contracts\Development;

interface IsDevelopmentEntity
{
    public function getLabel(): string;

    public function getStyleString(): string;

    public function getExtra(): array;
}
