<?php

namespace App\Rules;

use App\Models\Driver;
use Illuminate\Contracts\Validation\Rule;

class UniqueDriversInRequest implements Rule
{
    private ?string $driverId;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
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
        $this->driverId = $drivers->duplicates('driver_id')->first();

        return $this->driverId === null;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        $driver = Driver::find($this->driverId);
        return "Driver $driver->fullName has been added more than once.";
    }
}
