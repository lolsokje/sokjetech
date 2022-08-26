<?php

namespace App\Actions;

use App\Builders\SuggestionBuilder;
use App\Http\Requests\SuggestionFilterRequest;
use App\Models\Suggestion;
use Illuminate\Pagination\LengthAwarePaginator;

class GetSuggestions
{
    public function __construct(protected SuggestionFilterRequest $request)
    {
    }

    public function handle(): LengthAwarePaginator
    {
        return Suggestion::query()
            ->with('user')
            ->sort($this->request->field(), $this->request->direction())
            ->when($this->request->validated('only'), function (SuggestionBuilder $query, string $only) {
                $query->only($only);
            })
            ->paginate(perPage: 20, page: $this->request->page())
            ->withQueryString();
    }
}
