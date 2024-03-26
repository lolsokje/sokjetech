<?php

namespace App\Http\Resources\Race\Results;

use App\ValueObjects\Race\Results\DriverQualifyingPerformance;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin DriverQualifyingPerformance */
class DriverQualifyingPerformanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'position' => $this->position,
            'sessions' => $this->runs,
        ];
    }
}
