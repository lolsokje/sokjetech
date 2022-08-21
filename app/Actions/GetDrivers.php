<?php

namespace App\Actions;

use App\Builders\DriverBuilder;
use App\Http\Requests\DriverFilterRequest;
use App\Models\Driver;
use App\Models\Universe;
use Illuminate\Pagination\LengthAwarePaginator;

class GetDrivers
{
    public function __construct(protected DriverFilterRequest $request, protected Universe $universe)
    {
    }

    public function handle(): LengthAwarePaginator
    {
        return Driver::query()
            ->forUniverse($this->universe)
            ->when($this->request->validated('search'), function (DriverBuilder $query, string $search) {
                $query->search($search);
            })
            ->sort($this->request->validated('field'), $this->request->validated('direction'))
            ->paginate(20);
    }
}
