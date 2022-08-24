<?php

namespace App\Http\Resources;

use App\Models\Race;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Race */
class IndexPageRaceResource extends JsonResource
{
    public function toArray($request): array
    {
        $universe = $this->season->universe;
        $user = $universe->user;
        $completed = $this->completed_at;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'user' => $user->username,
            'avatar' => $user->avatar,
            'season' => $this->season->year,
            'series' => $this->season->series->name,
            'universe' => $universe->name,
            'diff_for_humans' => $completed->diffForHumans(),
            'completed_at' => $completed->format('d/m/Y H:i:s'),
        ];
    }
}
