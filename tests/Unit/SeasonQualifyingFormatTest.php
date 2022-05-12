<?php

use App\Models\QualifyingFormats\SingleSession;
use App\Models\QualifyingFormats\ThreeSessionElimination;
use App\Models\Season;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotEquals;

test('a qualifying format can be attached to a season', function () {
    [$seasonOne, $seasonTwo] = Season::factory(2)->create();
    $threeSessionEliminationFormat = ThreeSessionElimination::factory()->create();
    $singleSessionFormat = SingleSession::factory()->create();

    $seasonOne->qualifyingFormat()->associate($threeSessionEliminationFormat);
    $seasonTwo->qualifyingFormat()->associate($singleSessionFormat);

    assertEquals(ThreeSessionElimination::class, get_class($seasonOne->qualifyingFormat));
    assertEquals(SingleSession::class, get_class($seasonTwo->qualifyingFormat));

    assertNotEquals(SingleSession::class, get_class($seasonOne->qualifyingFormat));
    assertNotEquals(ThreeSessionElimination::class, get_class($seasonTwo->qualifyingFormat));
});
