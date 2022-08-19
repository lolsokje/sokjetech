<?php

namespace App\Http\Resources;

use App\Enums\ReliabilityReasonTypes;
use Illuminate\Http\Resources\Json\JsonResource;

class DnfReasonResource extends JsonResource
{
    public function toArray($request): array
    {
        $data = [];

        foreach ($this->resource as $reason) {
            $type = strtolower(ReliabilityReasonTypes::tryFrom($reason->type)->name);
            $data[$type][] = $reason->reason;
        }

        return $data;
    }
}
