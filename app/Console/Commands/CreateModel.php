<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CreateModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:fullModel {name} {--update}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a model with standard flags for this project (-cfmrR)';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $name = $this->argument('name');

        Artisan::call("make:model {$name} -cfmr");
        $this->info('Model and associated files created');

        Artisan::call("make:request {$name}CreateRequest");
        $this->info('Create request created');

        if ($this->option('update')) {
            Artisan::call("make:request {$name}UpdateRequest");
            $this->info('Update request created');
        }
    }
}
