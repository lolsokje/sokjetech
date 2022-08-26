<?php

namespace App\Builders;

use App\Enums\SuggestionStatus;
use Illuminate\Database\Eloquent\Builder;

class SuggestionBuilder extends Builder
{
    public function sort(string $field, string $direction): SuggestionBuilder
    {
        return $this->orderBy($field, $direction);
    }

    public function only(string $only): SuggestionBuilder
    {
        $cases = $only === 'open' ? SuggestionStatus::openCases() : SuggestionStatus::closedCases();
        return $this->whereIn('status', $cases);
    }
}
