<?php

use App\Actions\Season\Development\History\GetTeamDevelopmentHistory;
use App\Models\Entrant;
use App\Models\Race;
use App\Models\Season;
use App\Models\TeamDevelopmentHistory;

it('works', function (string $component) {
    $this->assertFalse(false);
    $season = Season::factory()->create();
    $races = Race::factory(3)->for($season)->create();
    $team = Entrant::factory()->for($season)->create([$component => 30]);

    foreach ($races as $race) {
        TeamDevelopmentHistory::factory()
            ->for($season)
            ->for($race)
            ->for($team)
            ->create([
                'component' => $component,
                'initial' => $team->getComponentRating($component),
                'development' => 1,
            ]);

        $team->update([$component => $team->getComponentRating($component) + 1]);
    }

    $results = (new GetTeamDevelopmentHistory)->handle(
        season: $season,
        races: $races,
        component: $component,
    );

    $result = $results[0];

    $this->assertEquals($team->full_name, $result->label);
    $this->assertEquals([31, 32, 33], $result->history);
    $this->assertEquals($team->accent_colour, $result->accent);
    $this->assertFalse($result->dash);
})->with('team components');

it('does not include teams without development history', function (string $component) {
    $season = Season::factory()->create();
    $races = Race::factory(2)->for($season)->create();
    $teams = Entrant::factory(2)->for($season)->create();

    TeamDevelopmentHistory::factory()
        ->for($season)
        ->for($teams[1])
        ->for($races[0])
        ->create([
            'component' => $component,
        ]);

    $results = (new GetTeamDevelopmentHistory)->handle(
        season: $season,
        races: $races,
        component: $component,
    );

    $this->assertCount(1, $results);
    $this->assertEquals($teams[1]->full_name, $results[0]->label);
})->with('team components');

it('sets the value to the previous value for races without dev', function (string $component) {
    $season = Season::factory()->create();
    $races = Race::factory(3)->for($season)->create();
    $team = Entrant::factory()->for($season)->create([$component => 30]);

    foreach ([$races[0], $races[2]] as $race) {
        TeamDevelopmentHistory::factory()
            ->for($season)
            ->for($race)
            ->for($team)
            ->create([
                'component' => $component,
                'initial' => $team->getComponentRating($component),
                'development' => 1,
            ]);

        $team->update([$component => $team->getComponentRating($component) + 1]);
    }

    $results = (new GetTeamDevelopmentHistory)->handle(
        season: $season,
        races: $races,
        component: $component,
    );

    $this->assertEquals([31, 31, 32], $results[0]->history);
})->with('team components');

it('sets the value to null for uncompleted races', function (string $component) {
    $season = Season::factory()->create();
    $race = Race::factory()->for($season)->completed()->create(['order' => 1]);
    Race::factory(2)->for($season)->sequence(
        ['order' => 2],
        ['order' => 3],
    )->create();
    $team = Entrant::factory()->for($season)->create([$component => 30]);

    TeamDevelopmentHistory::factory()
        ->for($season)
        ->for($race)
        ->for($team)
        ->create([
            'component' => $component,
            'initial' => $team->getComponentRating($component),
            'development' => 1,
        ]);

    $team->update([$component => $team->getComponentRating($component) + 1]);

    $results = (new GetTeamDevelopmentHistory)->handle(
        season: $season,
        races: $season->refresh()->races,
        component: $component,
    );

    $history = $results[0]->history;

    $this->assertEquals(31, $history[0]);
    $this->assertEquals(31, $history[1]);
    $this->assertNull($history[2]);
})->with('team components');

it('returns the value of the first dev round for all races before the first round', function (string $component) {
    $season = Season::factory()->create();
    $races = Race::factory(3)->for($season)->create();
    $team = Entrant::factory()->for($season)->create([$component => 30]);

    // First dev round happens before the third race of the season
    TeamDevelopmentHistory::factory()
        ->for($season)
        ->for($races[2])
        ->for($team)
        ->create([
            'component' => $component,
            'initial' => $team->getComponentRating($component),
            'development' => 1,
        ]);

    $team->update([$component => $team->getComponentRating($component) + 1]);

    $results = (new GetTeamDevelopmentHistory)->handle(
        season: $season,
        races: $races,
        component: $component,
    );

    $this->assertEquals([30, 30, 31], $results[0]->history);
})->with('team components');

it('returns nothing when no development has taken place yet', function (string $component) {
    $season = Season::factory()->create();
    $races = Race::factory(2)->for($season)->create();
    Entrant::factory(2)->for($season)->create();

    $results = (new GetTeamDevelopmentHistory)->handle(
        season: $season,
        races: $races,
        component: $component,
    );

    $this->assertEquals([], $results);
})->with('team components');
