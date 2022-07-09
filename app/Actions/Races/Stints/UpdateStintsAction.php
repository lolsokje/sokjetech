<?php

namespace App\Actions\Races\Stints;

class UpdateStintsAction extends BaseStintAction
{
    public function handle(): void
    {
        // stints can be updated up until the race has been started
        if ($this->race->started) {
            return;
        }

        $stints = $this->getStintsOrder();

        $this->race->stints()->delete();
        $stints->each(fn (array $stint) => $this->race->stints()->create($stint));
    }
}
