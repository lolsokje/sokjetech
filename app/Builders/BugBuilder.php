<?php

namespace App\Builders;

use App\Enums\BugStatus;
use Illuminate\Database\Eloquent\Builder;

class BugBuilder extends Builder
{
    public function sort(string $field, string $direction): BugBuilder
    {
        return $this->orderBy($field, $direction);
    }

    public function only(string $only): BugBuilder
    {
        $cases = $only === 'open' ? BugStatus::openCases() : BugStatus::closedCases();
        return $this->whereIn('status', $cases);
    }
}
