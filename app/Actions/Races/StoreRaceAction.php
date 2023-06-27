<?php

namespace App\Actions\Races;

use App\Http\Requests\RaceCreateRequest;
use App\Models\Race;
use App\Models\Season;

class StoreRaceAction
{
    public function __construct(protected RaceCreateRequest $request, protected Season $season)
    {
    }

    public function handle(): Race
    {
        $data = array_merge([
            'order' => $this->getOrder(),
        ], $this->request->validated());

        return $this->season->races()->create($data);
    }

    private function getOrder(): int
    {
        $lastRace = $this->season->races()->latest('order')->first();

        return $lastRace ? $lastRace->order + 1 : 1;
    }
}
