<?php

namespace App\Actions\Ratings;

use App\Models\Racer;

class UpdateDriverRatings
{
    public function __construct(protected array $drivers, protected string $type = 'rating')
    {
    }

    public function handle(): void
    {
        (new UpdateRatings($this->drivers, Racer::class, $this->type))->handle();
    }
}
