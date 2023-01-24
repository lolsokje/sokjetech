<?php

use App\Models\Universe;
use App\Support\DriverGenerator\Generator;

$start = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', now()->sub('30 years'));
$end = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', now()->sub('18 years'));

dataset('dates', [['start' => $start, 'end' => $end]]);

it('can generate random drivers', function (DateTimeImmutable $start, DateTimeImmutable $end) {
    $generator = new Generator();

    $driver = $generator->generate($start, $end);

    $this->assertCount(1, $driver);

    $this->assertArrayHasKey('first_name', $driver[0]);
    $this->assertArrayHasKey('last_name', $driver[0]);
    $this->assertArrayHasKey('dob', $driver[0]);
    $this->assertArrayHasKey('country', $driver[0]);
})->with('dates');

test('the provided language must be valid', function () {
    new Generator('invalid');
})->expectException(InvalidArgumentException::class);

it('can persist drivers to the database', function (DateTimeImmutable $start, DateTimeImmutable $end) {
    $universe = Universe::factory()->create();

    $generator = new Generator();

    $drivers = $generator->generate($start, $end, 10);
    $generator->persist($universe, $drivers);

    $this->assertDatabaseCount('drivers', 10);

    foreach ($drivers as $driver) {
        $this->assertDatabaseHas('drivers', [
            'first_name' => $driver['first_name'],
            'last_name' => $driver['last_name'],
            'country' => $driver['country'],
            'universe_id' => $universe->id,
        ]);
    }
})->with('dates');
