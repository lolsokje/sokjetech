<?php

namespace App\Rules;

use App\Models\Lineup;
use App\Models\Season;
use Illuminate\Contracts\Validation\Rule;

class UniqueActiveDriversInSeason implements Rule
{
    private Season $season;
    private string $name;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(array $parameters)
    {
        $this->season = $parameters['season'];
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
        $lineup = Lineup::query()
            ->where('season_id', $this->season->id)
            ->where('driver_id', $value)
            ->where('active', true)
            ->first();

        if ($lineup) {
            $this->name = $lineup->driver->fullName;
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return "Driver {$this->name} is already active for another team this season.";
    }
}
