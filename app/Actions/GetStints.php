<?php

namespace App\Actions;

use App\Builders\StintBuilder;
use App\Http\Requests\StintFilterRequest;
use App\Models\Stint;
use Illuminate\Support\Collection;

class GetStints
{
    public function __construct(protected StintFilterRequest $request)
    {
    }

    public function handle(): Collection
    {
        return Stint::query()
            ->grouped()
            ->ordered()
            ->when($this->request->min(), fn (StintBuilder $builder, int $min) => $builder->minRng($min))
            ->when($this->request->max(), fn (StintBuilder $builder, int $max) => $builder->maxRng($max))
            ->when($this->request->reliability() !== null, function (StintBuilder $builder) {
                $builder->reliability($this->request->reliability());
            })
            ->when($this->request->useTeamRating() !== null, function (StintBuilder $builder) {
                $builder->useTeamRating($this->request->useTeamRating());
            })
            ->when($this->request->useDriverRating() !== null, function (StintBuilder $builder) {
                $builder->useDriverRating($this->request->useDriverRating());
            })
            ->when($this->request->useEngineRating() !== null, function (StintBuilder $builder) {
                $builder->useEngineRating($this->request->useEngineRating());
            })
            ->get();
    }
}
