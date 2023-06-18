<?php

use App\Support\LaptimeFormatter;

it('correctly formats to string', function () {
    $this->assertEquals('1:23.456', LaptimeFormatter::toString(83456));
});

it('correctly format to integer', function () {
    $this->assertEquals(83456, LaptimeFormatter::toInteger('1:23.456'));
});

it('correctly pads strings', function () {
    $this->assertEquals('1:01.001', LaptimeFormatter::toString(61001));
});

test('the provided string must be valid', function (string $laptime) {
    $this->assertEquals(83456, LaptimeFormatter::toInteger($laptime));
})->throws(InvalidArgumentException::class, "The provided lap time must follow the format [mm:ss.xxx]")
    ->with([
        '1.23.456',
        '1:23:456',
        '1:234.45',
    ]);
