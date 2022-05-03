<?php

namespace App\Actions\Stints;

use App\Actions\Action;

class CreateStintsAction extends StintAction implements Action
{
    public function handle(): void
    {
        $stints = $this->getStintsOrder($this->stints);

        $stints->each(fn (array $stint) => $this->race->stints()->create($stint));
    }
}
