<?php

namespace App\Actions;

use App\Builders\CircuitBuilder;
use App\Http\Requests\CircuitFilterRequest;
use App\Models\Circuit;
use Illuminate\Pagination\LengthAwarePaginator;

class GetSharedCircuits
{
    public function __construct(protected CircuitFilterRequest $request)
    {
    }

    public function handle(): LengthAwarePaginator
    {
        return Circuit::query()
            ->sort($this->request->field(), $this->request->direction())
            ->when($this->request->search(), function (CircuitBuilder $builder, string $search) {
                $builder->search($search);
            })
            ->shared()
            ->with('user')
            ->paginate(20)
            ->withQueryString();
    }
}
