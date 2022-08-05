<?php

namespace App\Actions\Ratings;

use App\Models\EngineSeason;

class UpdateEngineRatings
{
    public function __construct(protected array $engines, protected string $type = 'rating')
    {
    }

    public function handle(): void
    {
        (new UpdateRatings($this->engines, EngineSeason::class, $this->type))->handle();
    }
}
