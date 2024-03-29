<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

class CircuitBuilder extends Builder
{
    public function owned(): CircuitBuilder
    {
        return $this->where('user_id', (int) auth()->user()?->id);
    }

    public function search(string $search): CircuitBuilder
    {
        return $this->where(function (CircuitBuilder $query) use ($search) {
            $query->where('name', 'LIKE', '%' . $search . '%')
                ->orWhere('country', 'LIKE', '%' . $search . '%');
        });
    }

    public function sort(string $field, string $direction): CircuitBuilder
    {
        return $this->orderBy($field, $direction);
    }

    public function shared(): CircuitBuilder
    {
        return $this->where('shared', true)
            ->groupBy('name')
            ->groupBy('country');
    }
}
