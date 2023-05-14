<?php

namespace App\Http\Resources\RaceWeekend;

use App\Models\QualifyingResult;
use App\Support\Http\CustomJsonResource;
use Illuminate\Http\Request;

/** @mixin QualifyingResult */
class QualifyingResultResource extends CustomJsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'position' => $this->position,
            'sessions' => $this->runs,
        ];
    }

    public static function default(): array
    {
        return [
            'position' => null,
            'sessions' => [
                1 => [
                    'position' => null,
                    'runs' => [],
                ],
            ],
        ];
    }
}
