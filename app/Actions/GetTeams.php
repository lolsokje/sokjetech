<?php

namespace App\Actions;

use App\Builders\TeamBuilder;
use App\Http\Requests\TeamFilterRequest;
use App\Models\Team;
use App\Models\Universe;
use Illuminate\Pagination\LengthAwarePaginator;

class GetTeams
{
    public function __construct(protected TeamFilterRequest $request, protected Universe $universe)
    {
    }

    public function handle(): LengthAwarePaginator
    {
        return Team::query()
            ->forUniverse($this->universe)
            ->sort($this->request->field(), $this->request->direction())
            ->when($this->request->search(), function (TeamBuilder $builder, string $search) {
                $builder->search($search);
            })
            ->paginate(20);
    }
}
