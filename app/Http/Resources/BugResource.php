<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BugResource extends JsonResource
{
    public function toArray($request): array
    {
        $this->resource->load('reportedBy');
        return [
            'id' => $this->id,
            'user' => $this->reportedBy->username,
            'type' => $this->type,
            'summary' => $this->summary,
            'details' => $this->details,
            'status' => $this->status_text,
        ];
    }
}
