<?php

use App\Models\PointSystem;
use App\Models\QualifyingFormats\SingleSession;
use App\Models\Race;
use App\Models\ReliabilityConfiguration;
use App\Models\Season;
use App\Models\Series;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\put;
use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertTrue;

test('a universe owner can create seasons', function () {
    $user = User::factory()->create();
    $series = createSeriesForUser($user);

    $this->actingAs($user)
        ->post(route('series.seasons.store', [$series]), [
            'year' => 2021,
        ])
        ->assertRedirect(route('series.seasons.index', [$series]));

    $this->assertDatabaseCount('seasons', 1);
    $this->assertCount(1, $series->seasons);
});

test('an unauthenticated user can\'t create seasons', function () {
    $series = Series::factory()->create();

    $this->post(route('series.seasons.store', [$series]), [
        'year' => 2021,
    ])
        ->assertForbidden();

    $this->assertDatabaseCount('seasons', 0);
    $this->assertCount(0, $series->seasons);
});

test('an authenticated user can\'t create seasons in another user\'s universe', function () {
    $user = User::factory()->create();
    $series = Series::factory()->create();

    $this->actingAs($user)
        ->post(route('series.seasons.store', [$series]), [
            'year' => 2021,
        ])
        ->assertForbidden();

    $this->assertDatabaseCount('seasons', 0);
    $this->assertCount(0, $series->seasons);
});

test('a universe owner can update their seasons', function () {
    $user = User::factory()->create();
    $series = createSeriesForUser($user);
    $season = Season::factory()->for($series)->create();

    $this->actingAs($user)
        ->put(route('series.seasons.update', [$series, $season]), [
            'year' => 2022,
        ])
        ->assertRedirect(route('series.seasons.index', [$series]));

    $this->assertEquals(2022, $season->fresh()->year);
});

test('an unauthenticated user can\'t update seasons', function () {
    $season = Season::factory()->create();
    $year = $season->year;

    $this->put(route('series.seasons.update', [$season->series, $season]), [
        'year' => 2022,
    ])
        ->assertForbidden();

    $this->assertEquals($year, $season->fresh()->year);
});

test('an authenticated user can\'t update another user\'s seasons', function () {
    $user = User::factory()->create();
    $season = Season::factory()->create();
    $year = $season->year;

    $this->actingAs($user)
        ->put(route('series.seasons.update', [$season->series, $season]), [
            'year' => 2022,
        ])
        ->assertForbidden();

    $this->assertEquals($year, $season->fresh()->year);
});

test('a universe owner can view the season create page', function () {
    $user = User::factory()->create();
    $series = createSeriesForUser($user);

    $this->actingAs($user)
        ->get(route('series.seasons.create', [$series]))
        ->assertOk();
});

test('an unauthenticated user can\'t view the season create page', function () {
    $series = Series::factory()->create();

    $this->get(route('series.seasons.create', [$series]))
        ->assertRedirect(route('index'));
});

test('an authenticated user can\'t view another user\'s season create page', function () {
    $user = User::factory()->create();
    $series = Series::factory()->create();

    $this->actingAs($user)
        ->get(route('series.seasons.create', [$series]))
        ->assertForbidden();
});

test('a universe owner can view the season edit page', function () {
    $user = User::factory()->create();
    $series = createSeriesForUser($user);
    $season = Season::factory()->for($series)->create();

    $this->actingAs($user)
        ->get(route('series.seasons.edit', [$series, $season]))
        ->assertOk();
});

test('an unauthenticated user can\'t view the season edit page', function () {
    $series = Series::factory()->create();
    $season = Season::factory()->for($series)->create();

    $this->get(route('series.seasons.edit', [$series, $season]))
        ->assertRedirect(route('index'));
});

test('an authenticated user can\'t view another user\'s season edit page', function () {
    $user = User::factory()->create();
    $series = Series::factory()->create();
    $season = Season::factory()->for($series)->create();

    $this->actingAs($user)
        ->get(route('series.seasons.edit', [$series, $season]))
        ->assertForbidden();
});

test('a year has to be unique within a series', function () {
    $user = User::factory()->create();
    $series = createSeriesForUser($user);
    Season::factory()->for($series)->create(['year' => 2021]);

    $this->actingAs($user)
        ->post(route('series.seasons.store', [$series]), [
            'year' => 2021,
        ])
        ->assertSessionHasErrors(['year' => 'The year must be unique in this series']);
});

test('a season can be updated while retaining the same year', function () {
    $user = User::factory()->create();
    $series = createSeriesForUser($user);
    $season = Season::factory()->for($series)->create(['year' => 2021]);

    $this->actingAs($user)
        ->put(route('series.seasons.update', [$series, $season]), [
            'year' => 2021,
        ])
        ->assertRedirect(route('series.seasons.index', [$series]));
});

it('shows all seasons for the selected series on the index page', function () {
    $user = User::factory()->create();
    $series = createSeriesForUser($user);
    Season::factory(5)->for($series)->create();

    $this->actingAs($user)
        ->get(route('series.seasons.index', [$series]))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Seasons/Index')
                ->has('series.seasons', 5),
        );
});

test('a universe owner can mark a season as started', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    prepareSeasonForStart($season);

    actingAs($user)
        ->put(route('seasons.start', [$season]))
        ->assertRedirect(route('seasons.races.index', [$season]));

    assertTrue($season->fresh()->started);
});

test('unauthorised users cannot mark a season as started', function () {
    $season = Season::factory()->create();

    put(route('seasons.start', [$season]))
        ->assertForbidden();

    actingAs(User::factory()->create())
        ->put(route('seasons.start', [$season]))
        ->assertForbidden();

    assertFalse($season->fresh()->started);
});

test('a season cannot be started if it is missing dependencies', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    $route = route('seasons.start', [$season]);

    actingAs($user)
        ->put($route)
        ->assertSessionHas('error');

    $format = SingleSession::factory()->create();
    $format->season()->save($season);
    put($route)
        ->assertSessionHas('error');

    PointSystem::factory()->for($season)->create();
    put($route)
        ->assertSessionHas('error');

    ReliabilityConfiguration::factory()->for($season)->create();
    put($route)
        ->assertSessionHas('error');

    assertFalse($season->fresh()->can_start);
    assertFalse($season->fresh()->started);

    Race::factory()->for($season)->create();
    assertTrue($season->fresh()->can_start);

    put($route)
        ->assertSessionMissing('error');

    assertTrue($season->fresh()->started);
});

test('a universe owner can mark a season as completed', function () {
    $user = User::factory()->create();
    $season = tap(createSeasonForUser($user), function (Season $season) {
        $season->update(['started' => true]);
    });

    actingAs($user)
        ->put(route('seasons.complete', [$season]))
        ->assertRedirect(route('seasons.races.index', [$season]));

    assertTrue($season->fresh()->completed);
});

test('unauthorised users cannot mark a season as completed', function () {
    $season = tap(Season::factory()->create(), function (Season $season) {
        $season->update(['started' => true]);
    });

    put(route('seasons.complete', [$season]))
        ->assertForbidden();

    actingAs(User::factory()->create())
        ->put(route('seasons.complete', [$season]))
        ->assertForbidden();

    assertFalse($season->fresh()->completed);
});

test('a season cannot be completed if it has not been started', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    actingAs($user)
        ->put(route('seasons.complete', [$season]))
        ->assertSessionHas('error')
        ->assertRedirect(route('seasons.races.index', [$season]));

    assertFalse($season->fresh()->completed);
});

test('a season cannot be completed if it has unfinished races', function () {
    $user = User::factory()->create();
    $season = tap(createSeasonForUser($user), function (Season $season) {
        $season->update(['started' => true]);
    });

    Race::factory()->for($season)->create();

    actingAs($user)
        ->put(route('seasons.complete', [$season]))
        ->assertSessionHas('error')
        ->assertRedirect(route('seasons.races.index', [$season]));

    assertFalse($season->fresh()->completed);
});

function prepareSeasonForStart(Season $season): void
{
    $format = SingleSession::factory()->create();
    $format->season()->save($season);
    PointSystem::factory()->for($season)->create();
    ReliabilityConfiguration::factory()->for($season)->create();
    Race::factory()->for($season)->create();
}
