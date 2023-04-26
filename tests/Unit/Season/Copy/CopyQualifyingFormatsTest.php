<?php

use App\Actions\Season\Copy\CopyQualifyingFormat;
use App\Exceptions\InvalidSeasonRequirements;
use App\Models\QualifyingFormats\SingleSession;
use App\Models\User;

it('copies the qualifying format', function () {
    [$season, $newSeason] = prepareQualifyingFormatForCopying();

    (new CopyQualifyingFormat($season, $newSeason))->handle();

    $this->assertDatabaseCount('single_sessions', 2);

    $this->assertNotNull($newSeason->qualifyingFormat);
});

test('a format most exist in the old season before copying', function () {
    [$season, $newSeason] = prepareQualifyingFormatForCopying();

    $season->qualifyingFormat()->delete();

    $this->assertDatabaseCount('single_sessions', 0);

    (new CopyQualifyingFormat($season, $newSeason))->handle();
})->throws(InvalidSeasonRequirements::class);

it('removes the existing qualifying format from the new season before copying', function () {
    [$season, $newSeason] = prepareQualifyingFormatForCopying();

    (new CopyQualifyingFormat($season, $newSeason))->handle();
    (new CopyQualifyingFormat($season, $newSeason))->handle();

    $this->assertDatabaseCount('single_sessions', 2);

    $this->assertNotNull($newSeason->qualifyingFormat);
});

function prepareQualifyingFormatForCopying(): array
{
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $format = SingleSession::factory()->create();
    $season->qualifyingFormat()->associate($format);
    $season->save();

    $newSeason = createSeasonForUser($user);

    test()->actingAs($user);

    return [$season, $newSeason];
}
