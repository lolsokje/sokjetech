<?php

declare(strict_types=1);

namespace App\Contracts\Development;

use Illuminate\Database\Eloquent\Builder;

interface HasRatingHistory
{
    public function getComponentRating(string $component): ?int;

    public function getHistoryTableQuery(): Builder;

    public function getForeignKey();

    public function getKey();
}
