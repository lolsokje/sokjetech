<?php

use App\Actions\Circuits\GetCircuitVariationsForSharedCircuit;
use App\Models\Circuit;
use App\Models\CircuitVariation;

it('returns all variations for the selected shared circuit', function () {
    [$c1, $c2] = Circuit::factory(2)->create(['name' => 'Circuit']);
    $c3 = Circuit::factory()->create(['name' => 'Different circuit']);

    CircuitVariation::factory()->for($c1)->create();
    CircuitVariation::factory(2)->for($c2)->create();
    CircuitVariation::factory()->for($c3)->create();

    $action = new GetCircuitVariationsForSharedCircuit;

    $result = $action->handle($c1->name);
    $this->assertCount(3, $result);

    $result = $action->handle($c3->name);
    $this->assertCount(1, $result);
});
