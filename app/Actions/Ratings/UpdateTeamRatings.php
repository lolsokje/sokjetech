<?php

namespace App\Actions\Ratings;

use App\Models\Entrant;

class UpdateTeamRatings
{
    public function __construct(protected array $teams, protected string $type = 'rating')
    {
    }

    public function handle(): void
    {
        (new UpdateRatings($this->teams, Entrant::class, $this->type))->handle();
    }
}
