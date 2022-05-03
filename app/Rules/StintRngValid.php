<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class StintRngValid implements Rule
{
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
        $stints = collect($value);

        return ! $stints->some(fn ($stint) => $stint['min_rng'] >= $stint['max_rng']);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'Min stint RNG must be lower than max stint RNG';
    }
}
