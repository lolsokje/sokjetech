<?php

namespace App\Actions;

use App\Builders\UniverseBuilder;
use App\Http\Requests\UniverseFilterRequest;
use App\Models\Universe;
use Illuminate\Pagination\LengthAwarePaginator;

class GetUniverses
{
    public function __construct(protected UniverseFilterRequest $request)
    {
    }

    public function handle(): LengthAwarePaginator
    {
        return Universe::query()
            ->with('user')
            ->visible()
            ->when($this->request->validated('search'), function (UniverseBuilder $builder, string $search) {
                $builder->search($search);
            })
            ->when($this->request->onlyMine(), function (UniverseBuilder $builder) {
                $builder->where('user_id', (int) auth()->user()?->id);
            })
            ->orderBy('name', $this->request->validated('direction', 'asc'))
            ->paginate(20);
    }
}
