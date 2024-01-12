<?php

namespace App\Actions\Races\Results;

use App\DataTransferObjects\Race\Result\RaceResult as RaceResultDTO;
use App\DataTransferObjects\Race\Result\RaceResultCollection;
use App\Models\RaceResult;

final readonly class UpdateRaceResults
{
    public function handle(RaceResultCollection $results): void
    {
        foreach ($results->results as $result) {
            $this->updateRaceResult($result);
        }
    }

    private function updateRaceResult(RaceResultDTO $result): void
    {
        RaceResult::query()->where('id', $result->id)->update([
            'position' => $result->position,
            'stints' => $result->stints,
            'total' => $result->total,
        ]);
    }
}
