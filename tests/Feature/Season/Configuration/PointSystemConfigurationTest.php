<?php

use App\Models\PointDistribution;
use App\Models\PointSystem;
use App\Models\Season;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('a universe owner can view the point configuration page', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    $this->actingAs($user)
        ->get(route('seasons.configuration.points', [$season]))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Seasons/Configuration/Points')
                ->has('season'),
        );
});

test('an unauthenticated user cannot view a point configuration page', function () {
    $season = Season::factory()->create();

    $this->get(route('seasons.configuration.points', [$season]))
        ->assertForbidden();
});

test('an authenticated user cannot view another users points configuration page', function () {
    $user = User::factory()->create();
    $season = Season::factory()->create();

    $this->actingAs($user)
        ->get(route('seasons.configuration.points', [$season]))
        ->assertForbidden();
});

test('a universe owner can store a point system', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    $this->actingAs($user)
        ->post(route('seasons.configuration.points.store', [$season]), getPointSystemData())
        ->assertRedirect(route('seasons.configuration.points', [$season]));

    $this->assertDatabaseCount('point_systems', 1);
    $this->assertDatabaseCount('point_distributions', 10);
    $this->assertNotNull($season->pointSystem);
    $this->assertCount(10, $season->pointDistribution);
});

it('does not allow negative points', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    $this->actingAs($user)
        ->post(route('seasons.configuration.points.store', [$season]), [
            'points' => [
                ['position' => 1, 'points' => -1],
                ['position' => 2, 'points' => 1],
            ],
        ])
        ->assertInvalid(['points' => 'The awarded points to a position can\'t be negative.']);

    $this->assertNull($season->fresh()->pointSystem);
    $this->assertCount(0, $season->fresh()->pointDistribution);
});


test('an unauthenticated user cannot store a point system', function () {
    $season = Season::factory()->create();

    $this->post(route('seasons.configuration.points.store', [$season]), getPointSystemData())
        ->assertForbidden();

    $this->assertDatabaseCount('point_systems', 0);
    $this->assertDatabaseCount('point_distributions', 0);
    $this->assertNull($season->pointSystem);
    $this->assertCount(0, $season->pointDistribution);
});

test('an authenticated user cannot store a point system in another users universe', function () {
    $user = User::factory()->create();
    $season = Season::factory()->create();

    $this->actingAs($user)
        ->post(route('seasons.configuration.points.store', [$season]), getPointSystemData())
        ->assertForbidden();

    $this->assertDatabaseCount('point_systems', 0);
    $this->assertDatabaseCount('point_distributions', 0);
    $this->assertNull($season->pointSystem);
    $this->assertCount(0, $season->pointDistribution);
});

test('the existing point system will be removed when updating', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $system = PointSystem::factory()->for($season)->create();
    PointDistribution::factory(10)->for($system)->create();

    $this->actingAs($user)
        ->post(route('seasons.configuration.points.store', [$season]), getPointSystemData())
        ->assertRedirect(route('seasons.configuration.points', [$season]));

    $this->assertDatabaseCount('point_systems', 1);
    $this->assertDatabaseCount('point_distributions', 10);
    $this->assertNotNull($season->pointSystem);
    $this->assertCount(10, $season->pointDistribution);
});

test('a point system cannot be saved when a season has started', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $season->update(['started' => true]);

    $this->actingAs($user)
        ->post(route('seasons.configuration.points.store', [$season]), getPointSystemData())
        ->assertRedirect();
});

function getPointSystemData(?array $override = []): array
{
    return array_merge([
        'points' => [
            ['position' => 1, 'points' => 10],
            ['position' => 2, 'points' => 9],
            ['position' => 3, 'points' => 8],
            ['position' => 4, 'points' => 7],
            ['position' => 5, 'points' => 6],
            ['position' => 6, 'points' => 5],
            ['position' => 7, 'points' => 4],
            ['position' => 8, 'points' => 3],
            ['position' => 9, 'points' => 2],
            ['position' => 10, 'points' => 1],
        ],
        'pole_position_point_awarded' => false,
        'fastest_lap_point_awarded' => false,
    ], $override);
}
