<?php

namespace App\Actions\Races\Results;

use App\Models\QualifyingResult;
use Illuminate\Support\Collection;

final readonly class GetQualifyingResults
{
    /**
     * @return Collection<QualifyingResult>
     */
    public function handle(int $raceId): Collection
    {
        return QualifyingResult::query()
            ->where('race_id', $raceId)
            ->orderBy('position')
            ->with([
                'racer' => [
                    'driver',
                    'entrant',
                ],
            ])
            ->get();
    }
}
