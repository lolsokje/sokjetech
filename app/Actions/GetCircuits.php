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
            ->with('defaultClimate')
            ->withCount('races')
            ->owned()
            ->when($this->request->search(), function (CircuitBuilder $builder, string $search) {
                return $builder->search($search);
            })
            ->sort($this->request->field(), $this->request->direction())
            ->paginate(20)
            ->withQueryString();
    }
}
