<?php

namespace App\Builders;

use App\Models\Universe;
use Illuminate\Database\Eloquent\Builder;

class DriverBuilder extends Builder
{
    public function forUniverse(Universe $universe): DriverBuilder
    {
        return $this->where('universe_id', $universe->id);
    }

    public function search(string $search): DriverBuilder
    {
        return $this->whereRaw('CONCAT(first_name, last_name) LIKE "%' . $search . '%"');
    }

    public function sort(?string $field, ?string $direction): DriverBuilder
    {
        return $this->orderBy($field ?? 'first_name', $direction ?? 'asc');
    }
}
