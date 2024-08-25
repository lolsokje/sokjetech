<?php

declare(strict_types=1);

namespace App\Http\Resources\Season\Development;

use App\Models\Race;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Race */
final class DevelopmentHistoryRaceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        parent::wrap(false);

        return [
            'race' => $this->name,
            'country' => $this->circuit->country,
        ];
    }
}
