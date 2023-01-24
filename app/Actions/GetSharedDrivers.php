<?php

namespace App\Actions;

use App\Builders\DriverBuilder;
use App\Http\Requests\DriverFilterRequest;
use App\Models\Driver;
use Illuminate\Pagination\LengthAwarePaginator;

class GetSharedDrivers
{
    public function __construct(protected DriverFilterRequest $request)
    {
    }

    public function handle(): LengthAwarePaginator
    {
        return Driver::query()
            ->sort($this->request->field(), $this->request->direction())
            ->when($this->request->search(), function (DriverBuilder $builder, string $search) {
                $builder->search($search);
            })
            ->shared()
            ->paginate(20)
            ->withQueryString();
    }
}
