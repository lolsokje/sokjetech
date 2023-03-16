<?php

use App\Enums\QualifyingFormat;
use App\Models\QualifyingFormats\SingleSession;
use App\Models\QualifyingFormats\ThreeSessionElimination;
use App\Models\Season;
use App\Models\User;

test('a universe owner can view the qualifying configuration page', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    $this->actingAs($user)
        ->get(route('seasons.configuration.qualifying', [$season]))
        ->assertOk();
});

test('unauthorised users cannot view the qualifying configuration page', function () {
    $season = Season::factory()->create();

    $this->get(route('seasons.configuration.qualifying', [$season]))
        ->assertForbidden();

    $this->actingAs(User::factory()->create())
        ->get(route('seasons.configuration.qualifying', [$season]))
        ->assertForbidden();
});

test('a universe owner can store a single session qualifying format', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    $this->actingAs($user)
        ->post(route('seasons.configuration.qualifying.store', $season), getSingleSessionRequestBody())
        ->assertRedirect(route('series.seasons.show', [$season->series, $season]));

    $this->assertEquals(SingleSession::class, get_class($season->fresh()->qualifyingFormat));
});

test('a universe owner can store a three session elimination format', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    $this->actingAs($user)
        ->post(route('seasons.configuration.qualifying.store', $season), [
            'selected_format' => QualifyingFormat::THREE_SESSION_ELIMINATION->value,
            'format_details' => [
                'q2_driver_count' => 1,
                'q3_driver_count' => 1,
                'runs_per_session' => 1,
                'min_rng' => 1,
                'max_rng' => 2,
            ],
        ])
        ->assertRedirect(route('series.seasons.show', [$season->series, $season]));

    $this->assertEquals(ThreeSessionElimination::class, get_class($season->fresh()->qualifyingFormat));
});

test('the format details are required', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    $this->actingAs($user)
        ->from(route('seasons.configuration.qualifying', $season))
        ->post(route('seasons.configuration.qualifying.store', $season), [
            'selected_format' => QualifyingFormat::SINGLE_SESSION->value,
        ])
        ->assertRedirect(route('seasons.configuration.qualifying', $season))
        ->assertSessionHasErrors(['format_details' => 'The format details field is required.']);
});

it('only stores one qualifying configuration for a season', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    $data = [
        'format_details' => [
            'min_rng' => 5,
            'max_rng' => 10,
            'runs_per_session' => 1,
        ],
    ];

    $this->actingAs($user)
        ->post(route('seasons.configuration.qualifying.store', [$season]), getSingleSessionRequestBody($data));

    $season = $season->fresh();
    $format = $season->qualifyingFormat;

    $this->assertNotNull($format);
    $this->assertEquals(1, $format->runs_per_session);
    $this->assertEquals(5, $format->min_rng);
    $this->assertEquals(10, $format->max_rng);

    $data = [
        'format_details' => [
            'min_rng' => 20,
            'max_rng' => 30,
            'runs_per_session' => 2,
        ],
    ];

    $this->actingAs($user)
        ->post(route('seasons.configuration.qualifying.store', [$season]), getSingleSessionRequestBody($data))
        ->assertRedirect();

    $season = $season->fresh();
    /** @var SingleSession $format */
    $format = $season->qualifyingFormat;

    $this->assertDatabaseCount('single_sessions', 1);
    $this->assertEquals(2, $format->runs_per_session);
    $this->assertEquals(20, $format->min_rng);
    $this->assertEquals(30, $format->max_rng);
});

test('the qualifying configuration can not be updated once the season has started', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $season->update(['started' => true]);

    $this->actingAs($user)
        ->from(route('seasons.configuration.qualifying', $season))
        ->post(route('seasons.configuration.qualifying.store', [$season], getSingleSessionRequestBody()))
        ->assertRedirect(route('seasons.configuration.qualifying', $season));
});

function getSingleSessionRequestBody(?array $override = []): array
{
    return array_merge([
        'selected_format' => QualifyingFormat::SINGLE_SESSION->value,
        'format_details' => [
            'runs_per_session' => 1,
            'min_rng' => 1,
            'max_rng' => 2,
        ],
    ], $override);
}
