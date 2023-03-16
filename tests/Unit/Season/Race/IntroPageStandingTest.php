<?php

use App\Actions\Races\CalculateIntroPageStandingsAction;
use App\Models\Entrant;
use App\Models\Race;
use App\Models\Racer;
use App\Models\RaceResult;
use App\Models\Season;

it('calculates the standings for the intro page', function () {
    [$season, $racerOne, $racerTwo, $racerThree, $entrantOne, $entrantTwo] = prepareIntroPage();

    [$driverResult, $teamResult] = (new CalculateIntroPageStandingsAction($season->races->last()))->handle();

    $this->assertEquals(buildDriverResult($racerTwo, 20), $driverResult[0]);
    $this->assertEquals(buildDriverResult($racerOne, 18), $driverResult[1]);
    $this->assertEquals(buildDriverResult($racerThree, 14), $driverResult[2]);

    $this->assertEquals(buildTeamResult($entrantTwo, 34), $teamResult[0]);
    $this->assertEquals(buildTeamResult($entrantOne, 18), $teamResult[1]);
});

function prepareIntroPage(): array
{
    $season = Season::factory()->create();

    [$entrantOne, $entrantTwo] = Entrant::factory(2)->for($season)->create();

    $racerOne = Racer::factory()->for($season)->for($entrantOne)->create();
    $racerTwo = Racer::factory()->for($season)->for($entrantTwo)->create();
    $racerThree = Racer::factory()->for($season)->for($entrantTwo)->create();

    [$raceOne, $raceTwo] = Race::factory(3)->for($season)->sequence(
        ['order' => 1],
        ['order' => 2],
        ['order' => 3],
    )->create();

    $racerOneResults = [
        $raceOne->order => 10,
        $raceTwo->order => 8,
    ];

    $racerTwoResults = [
        $raceOne->order => 10,
        $raceTwo->order => 10,
    ];

    $racerThreeResults = [
        $raceOne->order => 8,
        $raceTwo->order => 6,
    ];

    foreach ($racerOneResults as $order => $result) {
        $race = $season->races->where('order', $order)->first();
        RaceResult::factory()
            ->for($racerOne)
            ->for($race)
            ->for($season)
            ->for($entrantOne)
            ->create([
                'points' => $result,
            ]);
    }

    foreach ($racerTwoResults as $order => $result) {
        $race = $season->races->where('order', $order)->first();
        RaceResult::factory()
            ->for($racerTwo)
            ->for($race)
            ->for($season)
            ->for($entrantTwo)
            ->create([
                'points' => $result,
            ]);
    }

    foreach ($racerThreeResults as $order => $result) {
        $race = $season->races->where('order', $order)->first();
        RaceResult::factory()
            ->for($racerThree)
            ->for($race)
            ->for($season)
            ->for($entrantTwo)
            ->create([
                'points' => $result,
            ]);
    }

    return [$season, $racerOne, $racerTwo, $racerThree, $entrantOne, $entrantTwo];
}

function buildDriverResult(Racer $racer, int $points): array
{
    return [
        'points' => $points,
        'full_name' => $racer->driver->full_name,
        'number' => $racer->number,
        'background_colour' => $racer->entrant->accent_colour,
        'style_string' => $racer->entrant->style_string,
        'team_name' => $racer->entrant->short_name,
    ];
}

function buildTeamResult(Entrant $entrant, int $points): array
{
    return [
        "points" => $points,
        "full_name" => $entrant->full_name,
        "background_colour" => $entrant->accent_colour,
        "style_string" => $entrant->style_string,
        "team_principal" => $entrant->team_principal,
    ];
}
