<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

class StintBuilder extends Builder
{
    public function grouped(): StintBuilder
    {
        return $this->groupBy('min_rng')
            ->groupBy('max_rng')
            ->groupBy('reliability')
            ->groupBy('use_driver_rating')
            ->groupBy('use_team_rating')
            ->groupBy('use_engine_rating');
    }

    public function ordered(): StintBuilder
    {
        return $this->orderBy('min_rng')
            ->orderBy('max_rng')
            ->orderBy('reliability', 'DESC')
            ->orderBy('use_driver_rating', 'DESC')
            ->orderBy('use_team_rating', 'DESC')
            ->orderBy('use_engine_rating', 'DESC');
    }

    public function minRng(int $min): StintBuilder
    {
        return $this->where('min_rng', '>=', $min);
    }

    public function maxRng(int $max): StintBuilder
    {
        return $this->where('max_rng', '<=', $max);
    }

    public function reliability(bool $reliability): StintBuilder
    {
        return $this->where('reliability', $reliability);
    }

    public function useTeamRating(bool $useTeamRating): StintBuilder
    {
        return $this->where('use_team_rating', $useTeamRating);
    }

    public function useDriverRating(bool $useDriverRating): StintBuilder
    {
        return $this->where('use_driver_rating', $useDriverRating);
    }

    public function useEngineRating(bool $useEngineRating): StintBuilder
    {
        return $this->where('use_engine_rating', $useEngineRating);
    }
}
