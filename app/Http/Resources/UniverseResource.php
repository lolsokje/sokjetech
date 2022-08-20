<?php

namespace App\Http\Resources;

use Gate;
use Illuminate\Http\Resources\Json\JsonResource;

class UniverseResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'user' => $this->resource->user->toArray(),
            'can' => [
                'edit' => Gate::check('owns-universe', $this->resource),
            ],
        ];
    }
}
