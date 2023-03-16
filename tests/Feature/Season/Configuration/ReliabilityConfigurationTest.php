<?php

use App\Models\Season;
use App\Models\User;

test('a universe owner can view the reliability configuration page', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    $this->actingAs($user)
        ->get(route('seasons.configuration.reliability', [$season]))
        ->assertOk();
});

test('unauthorised users cannot view the reliability configuration page', function () {
    $season = Season::factory()->create();

    $this->get(route('seasons.configuration.reliability', [$season]))
        ->assertForbidden();

    $this->actingAs(User::factory()->create())
        ->get(route('seasons.configuration.reliability', [$season]))
        ->assertForbidden();
});

test('a universe owner can store min and max reliability RNG', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    $this->actingAs($user)
        ->post(route('seasons.configuration.reliability.store', [$season]), getRequestBody())
        ->assertRedirect(route('seasons.configuration.reliability', [$season]));

    $this->assertNotNull($season->reliabilityConfiguration);
    $this->assertEquals(5, $season->reliabilityConfiguration->min_rng);
    $this->assertEquals(10, $season->reliabilityConfiguration->max_rng);
});

test('the min and max RNG values are required', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    $this->actingAs($user)
        ->post(route('seasons.configuration.reliability.store', [$season]))
        ->assertInvalid(['min_rng' => 'required', 'max_rng' => 'required']);

    $this->assertNull($season->reliabilityConfiguration);
});

it('only stores one reliability configuration for a season', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    $this->actingAs($user)
        ->post(route('seasons.configuration.reliability.store', [$season]), getRequestBody())
        ->assertRedirect(route('seasons.configuration.reliability', [$season]));

    $this->assertNotNull($season->reliabilityConfiguration);
    $this->assertEquals(5, $season->reliabilityConfiguration->min_rng);
    $this->assertEquals(10, $season->reliabilityConfiguration->max_rng);

    $this->actingAs($user)
        ->post(
            route('seasons.configuration.reliability.store', [$season]), getRequestBody([
            'min_rng' => 20,
            'max_rng' => 30,
        ]),
        )
        ->assertRedirect(route('seasons.configuration.reliability', [$season]));

    $this->assertDatabaseCount('reliability_configurations', 1);
    $this->assertEquals(20, $season->fresh()->reliabilityConfiguration->min_rng);
    $this->assertEquals(30, $season->fresh()->reliabilityConfiguration->max_rng);
});

test('unauthorised users cannot store reliability configurations', function () {
    $season = Season::factory()->create();

    $this->post(route('seasons.configuration.reliability.store', [$season]), getRequestBody())
        ->assertForbidden();

    $this->actingAs(User::factory()->create())
        ->post(route('seasons.configuration.reliability.store', [$season]), getRequestBody())
        ->assertForbidden();

    $this->assertNull($season->reliabilityConfiguration);
});

test('a universe owner can store reliability reasons', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    $this->actingAs($user)
        ->post(route('seasons.configuration.reliability.store', [$season]), getRequestBody())
        ->assertRedirect(route('seasons.configuration.reliability', [$season]));

    $this->assertCount(6, $season->reliabilityReasons);
    $this->assertDatabaseCount('reliability_reasons', 6);
});

it('clears existing reasons before storing new ones', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    $this->actingAs($user)
        ->post(route('seasons.configuration.reliability.store', [$season]), getRequestBody())
        ->assertRedirect(route('seasons.configuration.reliability', [$season]));

    $this->post(route('seasons.configuration.reliability.store', [$season]), getRequestBody())
        ->assertRedirect(route('seasons.configuration.reliability', [$season]));

    $this->assertCount(6, $season->reliabilityReasons);
    $this->assertDatabaseCount('reliability_reasons', 6);
});

test('reasons must be delimited by a newline character', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    $body = getRequestBody([
        'reasons' => [
            'engine' => "reason 1 reason 2",
            'team' => "reason 3 reason 4",
            'driver' => "reason 4 reason 5",
        ],
    ]);

    $this->actingAs($user)
        ->post(route('seasons.configuration.reliability.store', [$season]), $body)
        ->assertRedirect(route('seasons.configuration.reliability', [$season]));

    $this->assertCount(3, $season->reliabilityReasons);
    $this->assertDatabaseCount('reliability_reasons', 3);

    $this->post(route('seasons.configuration.reliability.store', [$season]), getRequestBody())
        ->assertRedirect(route('seasons.configuration.reliability', [$season]));

    $this->assertCount(6, $season->reliabilityReasons()->get());
    $this->assertDatabaseCount('reliability_reasons', 6);
});

test('the reason types must exist', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    $this->actingAs($user)
        ->post(route('seasons.configuration.reliability.store', [$season]), getRequestBody(['reasons' => ['test' => 'reasons']]))
        ->assertInvalid(['reason_keys' => 'in']);

    $this->assertCount(0, $season->reliabilityReasons);
    $this->assertDatabaseCount('reliability_reasons', 0);
});

test('the reliability configuration can not be updated once the season has started', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $season->update(['started' => true]);

    $this->actingAs($user)
        ->from(route('seasons.configuration.reliability', $season))
        ->post(route('seasons.configuration.reliability.store', [$season], getRequestBody()))
        ->assertRedirect(route('seasons.configuration.reliability', $season));
});

function getRequestBody(?array $override = []): array
{
    $data = [
        'min_rng' => 5,
        'max_rng' => 10,
        'reasons' => [
            'engine' => "reason 5" . PHP_EOL . "reason 6",
            'team' => "reason 3" . PHP_EOL . "reason 4",
            'driver' => "reason 1" . PHP_EOL . "reason 2",
        ],
    ];

    return array_merge($data, $override);
}
