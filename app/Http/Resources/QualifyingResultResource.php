<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QualifyingResultResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'driver_id' => (string) $this->racer_id,
            'position' => $this->position,
            'runs' => $this->runs,
        ];
    }
}
