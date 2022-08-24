<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

class RaceBuilder extends Builder
{
    public function completed(): RaceBuilder
    {
        return $this->where('completed', true);
    }

    public function latestCompleted(): RaceBuilder
    {
        return $this->completed()
            ->orderBy('completed_at', 'DESC')
            ->limit(20);
    }

    public function visible(): RaceBuilder
    {
        return $this->whereRelation('season.series.universe', function (UniverseBuilder $query) {
            $query->visible();
        });
    }
}
