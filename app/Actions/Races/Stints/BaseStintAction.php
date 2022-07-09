<?php

namespace App\Actions\Races\Stints;

use App\Models\Race;
use Illuminate\Support\Collection;

class BaseStintAction
{
    public function __construct(protected Race $race, protected Collection $stints)
    {
    }

    protected function getStintsOrder(): Collection
    {
        return $this->stints->each(function (array $stint, int $key) {
            $this->stints[$key] = array_merge(['order' => $key + 1], $stint);
        });
    }
}
