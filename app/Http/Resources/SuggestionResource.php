<?php

namespace App\Http\Resources;

use App\Models\Suggestion;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Suggestion */
class SuggestionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user' => $this->user->username,
            'type' => $this->type,
            'summary' => $this->summary,
            'status' => $this->status_text,
        ];
    }
}
