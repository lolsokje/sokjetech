<?php

namespace App\Actions;

use App\Http\Requests\EngineFilterRequest;
use App\Models\Engine;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class GetSharedEngines
{
    public function __construct(protected EngineFilterRequest $request)
    {
    }

    public function handle(): LengthAwarePaginator
    {
        return Engine::query()
            ->where('shared', true)
            ->orderBy($this->request->field(), $this->request->direction())
            ->when($this->request->search(), function (Builder $builder, string $search) {
                $builder->where('name', 'LIKE', "%$search%");
            })
            ->orderBy('name')
            ->groupBy('name')
            ->paginate(20)
            ->withQueryString();
    }
}
