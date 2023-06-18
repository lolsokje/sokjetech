<?php

namespace App\Rules;

use App\Support\LaptimeFormatter;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidLaptimeRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! LaptimeFormatter::validateLaptime($value)) {
            $fail('The lap time must be in the format mm:ss.xxx');
        }
    }
}
