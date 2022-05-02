<?php

namespace App\Actions\Stints;

use App\Actions\ActionInterface;
use App\Models\Race;

class CreateStintsAction extends StintAction implements ActionInterface
{
    public function __construct(protected Race $race, protected array $stints)
    {
        parent::__construct($race, $stints);
    }

    public function handle(): void
    {
        $stints = $this->getStintsOrder($this->stints);

        $stints->each(fn(array $stint) => $this->race->stints()->create($stint));
    }
}
