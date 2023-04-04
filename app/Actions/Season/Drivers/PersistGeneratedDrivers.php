<?php

namespace App\Actions\Season\Drivers;

use App\Models\Universe;

class PersistGeneratedDrivers
{
    public function handle(Universe $universe, array $drivers): void
    {
        foreach ($drivers as $driver) {
            $universe->drivers()->create($driver);
        }
    }
}
