<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class UniqueNumbersInRequest implements Rule
{
    private ?int $number;

    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $drivers = collect($value);
        $this->number = $drivers->duplicates('number')->first();

        return $this->number === null;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return "The number {$this->number} is used by more than one driver in the current lineup.";
    }
}
