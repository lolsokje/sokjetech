<?php

namespace App\Rules;

use App\Models\Driver;
use Illuminate\Contracts\Validation\Rule;

class DriverNotRetiredRule implements Rule
{
    public function __construct()
    {
    }

    public function passes($attribute, $value): bool
    {
        return ! Driver::find($value)?->retired;
    }

    public function message(): string
    {
        return 'This driver is retired and can not be added to an entrant.';
    }
}
