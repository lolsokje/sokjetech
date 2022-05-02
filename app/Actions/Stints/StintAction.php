<?php

namespace App\Actions\Stints;

use App\Models\Race;
use Illuminate\Support\Collection;

class StintAction
{
    public function __construct(protected Race $race, protected array $stints)
    {
    }

    protected function getStintsOrder(array $stints): Collection
    {
        $ret = [];

        foreach ($stints as $key => $stint) {
            $order = $key + 1;
            $ret[] = array_merge(['order' => $order], $stint);
        }

        return collect($ret);
    }
}
