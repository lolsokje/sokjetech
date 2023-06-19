<?php

namespace App\Console\Commands;

use App\Models\Circuit;
use App\Support\LaptimeFormatter;
use Illuminate\Console\Command;
use Illuminate\Console\View\Components\Info;

class CreateDefaultCircuitVariationsCommand extends Command
{
    protected $signature = 'circuits:default-variations';

    protected $description = 'Creates default variations for all circuits without a variation';

    public function handle(): void
    {
        $circuits = Circuit::whereDoesntHave('variations')->get();

        foreach ($circuits as $circuit) {
            $circuit->variations()->create([
                'name' => $circuit->name,
                'length' => fake()->numberBetween(4500, 7000),
                'base_laptime' => LaptimeFormatter::toString(fake()->numberBetween(66000, 106000)),
            ]);
        }

        with(new Info($this->getOutput()))->render("Variations created");
    }
}
