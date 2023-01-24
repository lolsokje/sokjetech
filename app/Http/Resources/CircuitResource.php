<?php

namespace App\Http\Resources;

use App\Models\Circuit;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Circuit */
class CircuitResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'country' => $this->country,
            'shared' => $this->shared,
            'races_count' => $this->races_count ?? null,
        ];
    }
}
