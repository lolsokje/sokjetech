<?php

namespace App\Builders;

use App\Models\Universe;
use Illuminate\Database\Eloquent\Builder;

class TeamBuilder extends Builder
{
    public function forUniverse(Universe $universe): TeamBuilder
    {
        return $this->where('universe_id', (int) $universe->id);
    }

    public function sort(?string $field, ?string $direction): TeamBuilder
    {
        return $this->orderBy($field ?? 'full_name', $direction ?? 'asc');
    }

    public function search(string $search): TeamBuilder
    {
        $like = "%$search%";

        return $this->where(function (TeamBuilder $query) use ($like) {
            $query->where('full_name', 'LIKE', $like)
                ->orWhere('short_name', 'LIKE', $like)
                ->orWhere('team_principal', 'LIKE', $like);
        });
    }

    public function shared(): TeamBuilder
    {
        return $this
            ->where('shared', true)
            ->groupBy('short_name')
            ->groupBy('full_name')
            ->groupBy('team_principal');
    }
}
