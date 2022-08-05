<?php

namespace App\Actions\Ratings;

class UpdateRatings
{
    public function __construct(protected array $items, protected string $className, protected string $type)
    {
    }

    public function handle(): void
    {
        $items = collect($this->items);

        $items->each(function ($item) {
            $this->className::query()
                ->find($item['id'])
                ->update([$this->type => $item['new']]);
        });
    }
}
