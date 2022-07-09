<?php

namespace App\Actions\Races;

use App\Models\Race;
use Illuminate\Support\Collection;

class UpdateRaceOrderAction
{
    public function __construct(protected Collection $races)
    {
    }

    public function handle(): void
    {
        $this->races->each(function (array $race) {
            $dbRace = Race::find($race['id']);
            $dbRace->order = $race['order'];
            $dbRace->save();
        });
    }
}
