<?php

namespace App\Http\Resources;

use App\Models\Engine;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Engine */
class EngineResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
