<?php

use App\Actions\Season\Development\GetDevelopmentEngines;
use App\Models\EngineSeason;
use App\Models\Season;
use App\ValueObjects\Season\Development\DevelopmentEntity;

it('returns all engines for a season', function (string $component) {
    $season = Season::factory()->create();
    $engines = EngineSeason::factory(2)->sequence(
        ['rating' => 60, 'reliability' => 90, 'name' => 'a'],
        ['rating' => 70, 'reliability' => 80, 'name' => 'b'],
    )->for($season)->create();

    $developmentEngines = (new GetDevelopmentEngines)->handle($season, $component);

    $this->assertCount(2, $developmentEngines);
    $this->assertEquals($engines[0]->id, $developmentEngines[0]->id);
    $this->assertEquals($engines[1]->id, $developmentEngines[1]->id);

    $this->assertEquals($engines[0]->getComponentRating($component), $developmentEngines[0]->current);
    $this->assertEquals($engines[1]->getComponentRating($component), $developmentEngines[1]->current);
})->with('engine components');

it('can parse entities from requests', function (string $component) {
    $season = Season::factory()->create();
    $engine = EngineSeason::factory()->for($season)->create([
        $component => 60,
    ]);

    $entities = [
        [
            'id' => $engine->id,
            'label' => $engine->name,
            'current' => $engine->getComponentRating($component),
            'styleString' => '',
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

    $this->assertEquals($engine->id, $first->id);
    $this->assertEquals($engine->name, $first->label);
    $this->assertEquals($engine->getComponentRating($component), $first->current);
    $this->assertEquals(3, $first->rng);
    $this->assertEquals(63, $first->new);
})->with('engine components');
