<?php

namespace App\Actions\Stints;

use App\Actions\Action;

class UpdateStintsAction extends StintAction implements Action
{
    public function handle(): void
    {
        // stints can be updated up until the race has been started
        if ($this->race->started) {
            return;
        }

        $stints = $this->getStintsOrder($this->stints);

        $this->race->stints()->delete();
        $stints->each(fn (array $stint) => $this->race->stints()->create($stint));
    }
}
