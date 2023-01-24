<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidPointsRule implements Rule
{
    public function passes($attribute, $value): bool
    {
        $points = collect($value);
        return !$points->some(fn (array $value) => $value['points'] < 0);
    }

    public function message(): string
    {
        return 'The awarded points to a position can\'t be negative.';
    }
}
