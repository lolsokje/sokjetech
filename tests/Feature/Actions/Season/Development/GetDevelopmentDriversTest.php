<?php

use App\Actions\Season\Development\GetDevelopmentDrivers;
use App\Models\Racer;
use App\Models\Season;
use App\ValueObjects\Season\Development\DevelopmentEntity;

it('returns all active drivers for a season', function (string $component) {
    $season = Season::factory()->create();
    /** @var array<Racer> $drivers */
    $drivers = Racer::factory(2)->sequence(
        ['rating' => 60, 'reliability' => 90, 'number' => 1],
        ['rating' => 70, 'reliability' => 80, 'number' => 2],
    )->for($season)->create();

    Racer::factory()->for($season)->create(['active' => false]);

    $developmentDrivers = (new GetDevelopmentDrivers)->handle($season, $component);

    $this->assertCount(2, $developmentDrivers);
    $this->assertEquals($drivers[0]->id, $developmentDrivers[0]->id);
    $this->assertEquals($drivers[1]->id, $developmentDrivers[1]->id);

    $this->assertEquals($drivers[0]->getComponentRating($component), $developmentDrivers[0]->current);
    $this->assertEquals($drivers[1]->getComponentRating($component), $developmentDrivers[1]->current);
})->with('driver components');

it('can parse entities from requests', function (string $component) {
    $season = Season::factory()->create();
    $racer = Racer::factory()->for($season)->create([
        $component => 60,
    ]);

    $entities = [
        [
            'id' => $racer->id,
            'label' => $racer->driver->full_name,
            'current' => $racer->getComponentRating($component),
            'styleString' => $racer->entrant->style_string,
            'extra' => [],
            'min' => 0,
            'max' => 5,
            'rng' => 3,
            'new' => 63,
        ],
    ];

    $parsed = DevelopmentEntity::fromRequest($entities);

    $this->assertCount(1, $parsed);

    /** @var DevelopmentEntity $first */
    $first = $parsed->first();

    $this->assertEquals($racer->id, $first->id);
    $this->assertEquals($racer->driver->full_name, $first->label);
    $this->assertEquals($racer->getComponentRating($component), $first->current);
    $this->assertEquals(3, $first->rng);
    $this->assertEquals(63, $first->new);
})->with('driver components');
