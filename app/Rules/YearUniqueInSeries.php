<?php

namespace App\Rules;

use App\Models\Season;
use App\Models\Series;
use Illuminate\Contracts\Validation\Rule;

class YearUniqueInSeries implements Rule
{
    private Series $series;
    private ?Season $season;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(array $parameters)
    {
        $this->series = $parameters['series'];
        $this->season = $parameters['season'] ?? null;
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
        $query = Season::query()
            ->where('series_id', $this->series->id)
            ->where('year', $value);

        if ($this->season) {
            $query->where('id', '!=', $this->season->id);
        }

        return $query->count() === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'The year must be unique in this series';
    }
}
