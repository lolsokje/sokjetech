<?php

namespace App\Builders;

use App\Enums\UniverseVisibility;
use Illuminate\Database\Eloquent\Builder;

class UniverseBuilder extends Builder
{
    public function visible(): Builder
    {
        if (auth()->check()) {
            $this->where(function (Builder $query) {
                $query->where('visibility', UniverseVisibility::AUTH)
                    ->orWhere('user_id', auth()->user()->id);
            });
        }
        return $this->orWhere('visibility', UniverseVisibility::PUBLIC);
    }

    public function search(string $search): Builder
    {
        return $this->where('name', 'LIKE', "%$search%");
    }
}
