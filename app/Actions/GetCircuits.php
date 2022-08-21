<?php

namespace App\Actions;

use App\Builders\CircuitBuilder;
use App\Http\Requests\CircuitFilterRequest;
use App\Models\Circuit;
use Illuminate\Pagination\LengthAwarePaginator;

class GetCircuits
{
    public function __construct(protected CircuitFilterRequest $request)
    {
    }

    public function handle(): LengthAwarePaginator
    {
        return Circuit::query()
            ->owned()
            ->when($this->request->validated('search'), function (CircuitBuilder $builder, string $search) {
                return $builder->search($search);
            })
            ->sort($this->request->get('field'), $this->request->get('direction'))
            ->paginate(15);
    }
}
