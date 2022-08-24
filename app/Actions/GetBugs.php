<?php

namespace App\Actions;

use App\Builders\BugBuilder;
use App\Http\Requests\BugFilterRequest;
use App\Models\Bug;
use Illuminate\Pagination\AbstractPaginator;

class GetBugs
{
    public function __construct(protected BugFilterRequest $request)
    {
    }

    public function handle(): AbstractPaginator
    {
        return Bug::query()
            ->sort($this->request->field(), $this->request->direction())
            ->when($this->request->validated('only'), function (BugBuilder $query, string $only) {
                $query->only($only);
            })
            ->paginate(perPage: 20, page: $this->request->page())
            ->withQueryString();
    }
}
