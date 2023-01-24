<?php

namespace App\Actions;

use App\Builders\TeamBuilder;
use App\Http\Requests\TeamFilterRequest;
use App\Models\Team;
use Illuminate\Pagination\LengthAwarePaginator;

class GetSharedTeams
{
    public function __construct(protected TeamFilterRequest $request)
    {
    }

    public function handle(): LengthAwarePaginator
    {
        return Team::query()
            ->shared()
            ->sort($this->request->field(), $this->request->direction())
            ->when($this->request->search(), function (TeamBuilder $builder, string $search) {
                $builder->search($search);
            })
            ->paginate(20)
            ->withQueryString();
    }
}
