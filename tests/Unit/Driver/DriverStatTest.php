<?php

use App\Actions\Season\Drivers\GetBaseStats;
use App\Actions\Season\Drivers\GetCareerResultStats;
use App\Actions\Season\Standings\CalculateDriverChampionshipStandingsAction;
use App\DataTransferObjects\StatData;
use App\Http\Resources\Standings\RaceResultResource;
use App\Jobs\CalculateChampionshipStandingsJob;
use App\Models\Driver;
use App\Models\Race;
use App\Models\Racer;
use App\Models\RaceResult;
use App\Models\Season;
use App\Models\Series;

it('formats stat data', function () {
    $data = new StatData('wins', 1);

    $this->assertJsonStringEqualsJsonString('{"label":"wins","value":"1"}', json_encode($data));
});

it('calculates the base stats for a driver', function () {
    $driver = Driver::factory()->create();
    prepareStats($driver);

    $expectedStats = [
        ['label' => 'Starts', 'value' => 3],
        ['label' => 'Poles', 'value' => 2],
        ['label' => 'Points', 'value' => 52],
        ['label' => 'Podiums', 'value' => 2],
        ['label' => 'Wins', 'value' => 1],
        ['label' => 'Championships', 'value' => 1],
    ];

    /** @var StatData[] $stats */
    $stats = (new GetBaseStats($driver))->handle();

    foreach ($stats as $key => $stat) {
        $this->assertInstanceOf(StatData::class, $stat);

        $expected = $expectedStats[$key];

        $this->assertEquals($expected['label'], $stat->label);
        $this->assertEquals($expected['value'], $stat->value);
    }
});

it('calculates the combined stats for a driver', function () {
    $driver = Driver::factory()->create();
    [$series, $season] = prepareStats($driver);

    $results = (new GetCareerResultStats($driver))->handle();

    $this->assertEquals(buildExpectedCareerStatsArray($series, $season), $results);
});

test('a user can request base stats', function () {
    $driver = Driver::factory()->create();
    prepareStats($driver);

    $this->get(route('drivers.stats.base', $driver))
        ->assertOk()
        ->assertJsonFragment(['label' => 'Starts', 'value' => '3'])
        ->assertJsonFragment(['label' => 'Poles', 'value' => '2'])
        ->assertJsonFragment(['label' => 'Points', 'value' => '52'])
        ->assertJsonFragment(['label' => 'Podiums', 'value' => '2'])
        ->assertJsonFragment(['label' => 'Wins', 'value' => '1'])
        ->assertJsonFragment(['label' => 'Championships', 'value' => '1']);
});

test('a user can request combined career stats', function () {
    $driver = Driver::factory()->create();
    [$series, $season] = prepareStats($driver);

    $this->get(route('drivers.stats.combined', $driver))
        ->assertOk()
        ->assertJson(buildExpectedCareerStatsArray($series, $season));
});

function prepareStats(Driver $driver): array
{
    $series = Series::factory()->for($driver->universe)->create();
    $season = Season::factory()->for($series)->create();
    $racer = Racer::factory()->for($driver)->for($season)->create();
    $races = Race::factory(3)->for($season)->sequence(
        ['order' => 1],
        ['order' => 2],
        ['order' => 3],
    )->create();

    $results = [
        ['starting_position' => 1, 'position' => 1, 'points' => 25],
        ['starting_position' => 1, 'position' => 4, 'points' => 12],
        ['starting_position' => 5, 'position' => 3, 'points' => 15],
    ];

    foreach ($results as $key => $result) {
        RaceResult::factory()
            ->for($racer)
            ->for($season)
            ->for($races[$key])
            ->for($racer->entrant)
            ->create([
                'starting_position' => $result['starting_position'],
                'position' => $result['position'],
                'points' => $result['points'],
            ]);
    }

    (new CalculateChampionshipStandingsJob(
        new CalculateDriverChampionshipStandingsAction($season)
    ))->handle();

    return [$series, $season];
}

function buildExpectedCareerStatsArray(Series $series, Season $season): array
{
    $raceResults = [];

    foreach (RaceResult::query()->with('race')->get() as $result) {
        $raceResults[$result->race->order] = RaceResultResource::array($result);
    }

    return [
        $series->name => [
            $season->year => [
                'races' => $raceResults,
                'position' => 1,
                'points' => 52,
            ],
        ],
    ];
}
