<?php

namespace App\Actions\Stints;

use App\Actions\Action;

class UpdateStintsAction extends StintAction implements Action
{
    public function handle(): void
    {
        // TODO further checks to see if stints can be updated
        if ($this->race->completed) {
            return;
        }

        $stints = $this->getStintsOrder($this->stints);

        $this->race->stints()->delete();
        $stints->each(fn (array $stint) => $this->race->stints()->create($stint));
    }
}
