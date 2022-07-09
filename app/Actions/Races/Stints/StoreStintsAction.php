<?php

namespace App\Actions\Races\Stints;

class StoreStintsAction extends BaseStintAction
{
    public function handle(): void
    {
        $stints = $this->getStintsOrder();

        $stints->each(fn (array $stint) => $this->race->stints()->create($stint));
    }
}
