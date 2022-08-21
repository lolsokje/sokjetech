<?php

namespace App\Builders;

use App\Enums\UniverseVisibility;
use Illuminate\Database\Eloquent\Builder;

class UniverseBuilder extends Builder
{
    public function visible(): UniverseBuilder
    {
        return $this->where(function (UniverseBuilder $query) {
            if (auth()->check()) {
                $query->where('visibility', UniverseVisibility::AUTH)
                    ->orWhere('user_id', auth()->user()->id);
            }
            return $query->orWhere('visibility', UniverseVisibility::PUBLIC);
        });
    }

    public function search(string $search): UniverseBuilder
    {
        return $this->where('name', 'LIKE', "%$search%");
    }
}
