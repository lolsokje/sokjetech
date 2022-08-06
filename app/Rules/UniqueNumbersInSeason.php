<?php

namespace App\Rules;

use App\Models\Entrant;
use App\Models\Racer;
use App\Models\Season;
use Illuminate\Contracts\Validation\Rule;

class UniqueNumbersInSeason implements Rule
{
    private Season $season;
    private Entrant $entrant;
    private mixed $value;

    public function __construct(array $parameters)
    {
        $this->season = $parameters['season'];
        $this->entrant = $parameters['entrant'];
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     *
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $this->value = $value;

        $count = Racer::query()
            ->where('season_id', $this->season->id)
            ->where('number', $value)
            ->where('active', 1)
            ->where('entrant_id', '!=', (int) $this->entrant->id)
            ->count();

        return $count === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return "The number {$this->value} is already used by another active driver this season.";
    }
}
