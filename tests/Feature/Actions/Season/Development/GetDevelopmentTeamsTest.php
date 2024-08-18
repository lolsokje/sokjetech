<?php

use App\Actions\Season\Development\GetDevelopmentTeams;
use App\Models\Entrant;
use App\Models\Season;
use App\ValueObjects\Season\Development\DevelopmentEntity;

it('returns all teams for a season', function (string $component) {
    $season = Season::factory()->create();
    /** @var array<Entrant> $teams */
    $teams = Entrant::factory(2)->sequence(
        ['rating' => 60, 'reliability' => 90, 'full_name' => 'a'],
        ['rating' => 70, 'reliability' => 80, 'full_name' => 'b'],
    )->for($season)->create();

    Entrant::factory()->create(['active' => false]);

    $developmentTeams = (new GetDevelopmentTeams)->handle($season, $component);

    $this->assertCount(2, $developmentTeams);
    $this->assertEquals($teams[0]->id, $developmentTeams[0]->id);
    $this->assertEquals($teams[1]->id, $developmentTeams[1]->id);

    $this->assertEquals($teams[0]->getComponentRating($component), $developmentTeams[0]->current);
    $this->assertEquals($teams[1]->getComponentRating($component), $developmentTeams[1]->current);
})->with('team components');

it('can parse entities from requests', function (string $component) {
    $season = Season::factory()->create();
    $team = Entrant::factory()->for($season)->create([
        $component => 60,
    ]);

    $entities = [
        [
            'id' => $team->id,
            'label' => $team->full_name,
            'current' => $team->getComponentRating($component),
            'styleString' => $team->style_string,
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

    $this->assertEquals($team->id, $first->id);
    $this->assertEquals($team->full_name, $first->label);
    $this->assertEquals($team->getComponentRating($component), $first->current);
    $this->assertEquals(3, $first->rng);
    $this->assertEquals(63, $first->new);
})->with('team components');
