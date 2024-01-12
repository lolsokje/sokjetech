<?php

use App\Enums\RaceType;
use App\Models\Circuit;
use App\Models\CircuitVariation;
use App\Models\Climate;
use App\Models\Race;
use App\Models\Season;
use App\Models\Universe;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\delete;
use function PHPUnit\Framework\assertCount;
use function PHPUnit\Framework\assertEquals;

test('a universe owner can create races', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);

    $this->actingAs($user)
        ->post(route('seasons.races.store', [$season]), getRaceCreationData($season, $user))
        ->assertRedirect(route('seasons.races.index', [$season]));

    $this->assertDatabaseCount('races', 1);
    $this->assertCount(1, $season->races);
});

test('an unauthenticated user can\'t create races', function () {
    $season = Season::factory()->create();

    $this->post(route('seasons.races.store', [$season]), getRaceCreationData($season))
        ->assertForbidden();

    $this->assertDatabaseCount('races', 0);
    $this->assertCount(0, $season->races);
});

test('an authenticated user can\'t create races in another user\'s universe', function () {
    $user = User::factory()->create();
    $season = Season::factory()->create();

    $this->actingAs($user)
        ->post(route('seasons.races.store', [$season]), getRaceCreationData($season))
        ->assertForbidden();

    $this->assertDatabaseCount('races', 0);
    $this->assertCount(0, $season->races);
});

test('a universe owner can update races', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $race = Race::factory()->for($season)->create();

    $this->actingAs($user)
        ->put(route('seasons.races.update', [$season, $race]), [
            'circuit_id' => $race->circuit->id,
            'circuit_variation_id' => $race->variation->id,
            'climate_id' => Climate::factory()->create()->id,
            'name' => 'New name',
            'order' => $race->order,
            'race_type' => $race->race_type->value,
            'duration' => $race->duration,
        ])
        ->assertRedirect(route('seasons.races.index', [$season]));

    $this->assertEquals('New name', $race->fresh()->name);
});

test('an unauthenticated user can\'t update races', function () {
    $race = Race::factory()->create();
    $name = $race->name;

    $this->put(route('seasons.races.update', [$race->season, $race]), [
        'circuit_id' => $race->circuit->id,
        'name' => 'New name',
        'order' => $race->order,
    ])
        ->assertForbidden();

    $this->assertEquals($name, $race->fresh()->name);
});

test('an authenticated user can\'t update another user\'s race', function () {
    $user = User::factory()->create();
    $race = Race::factory()->create();
    $name = $race->name;

    $this->actingAs($user)
        ->put(route('seasons.races.update', [$race->season, $race]), [
            'circuit_id' => $race->circuit->id,
            'circuit_variation_id' => $race->variation->id,
            'climate_id' => Climate::factory()->create()->id,
            'name' => 'New name',
            'order' => $race->order,
            'race_type' => $race->race_type->value,
            'duration' => $race->duration,
        ])
        ->assertForbidden();

    $this->assertEquals($name, $race->fresh()->name);
});

it('adds new races after already existing races', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    Race::factory()->for($season)->create(['order' => 1]);

    $this->actingAs($user)
        ->post(route('seasons.races.store', [$season]), [
            'circuit_id' => Circuit::factory()->create()->id,
            'circuit_variation_id' => CircuitVariation::factory()->create()->id,
            'climate_id' => Climate::factory()->create()->id,
            'name' => 'Test race with automatically generated order',
            'race_type' => RaceType::LAP->value,
            'duration' => 50,
        ]);

    $race = $season->races()->where('order', 2)->first();
    $this->assertEquals('Test race with automatically generated order', $race->name);
});

it('adds new races after reordered races', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $race1 = Race::factory()->for($season)->create(['order' => 1]);
    $race2 = Race::factory()->for($season)->create(['order' => 2]);

    $race1->update(['order' => 2]);
    $race2->update(['order' => 1]);

    $this->actingAs($user)
        ->post(route('seasons.races.store', [$season]), [
            'circuit_id' => Circuit::factory()->create()->id,
            'circuit_variation_id' => CircuitVariation::factory()->create()->id,
            'climate_id' => Climate::factory()->create()->id,
            'name' => 'Test race with automatically generated order',
            'race_type' => RaceType::LAP->value,
            'duration' => 50,
        ]);

    $race = $season->races()->where('order', 3)->first();
    $this->assertEquals('Test race with automatically generated order', $race->name);
});

test('a universe owner can view the race create page', function () {
    $universe = Universe::factory()->create();
    $season = createSeasonForUser($universe->user);

    $this->actingAs($universe->user)
        ->get(route('seasons.races.create', $season))
        ->assertOk();
});

test('an unauthenticated user can\'t view the race create page', function () {
    $season = Season::factory()->create();

    $this->get(route('seasons.races.create', [$season]))
        ->assertRedirect(route('index'));
});

test('an authenticated user can\'t view the create race page in other universes', function () {
    $season = Season::factory()->create();

    $this->actingAs(User::factory()->create())
        ->get(route('seasons.races.create', [$season]))
        ->assertForbidden();
});

test('a universe owner can view the race edit page', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $race = Race::factory()->for($season)->create();

    $this->actingAs($user)
        ->get(route('seasons.races.edit', [$season, $race]))
        ->assertOk();
});

test('a universe owner can view the race reorder page', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    Race::factory(5)->for($season)->create();

    $this->actingAs($user)
        ->get(route('seasons.races.reorder', [$season]))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Races/Reorder')
                ->has('season.races', 5),
        );
});

test('an unauthenticated user can\'t view the race reorder page', function () {
    $race = Race::factory()->create();

    $this->get(route('seasons.races.reorder', [$race->season]))
        ->assertRedirect(route('index'));
});

test('an authenticated user can\'t view the race reorder page for other universes', function () {
    $race = Race::factory()->create();

    $this->actingAs(User::factory()->create())
        ->get(route('seasons.races.reorder', [$race->season]))
        ->assertForbidden();
});

test('a universe owner can reorder races', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $raceOne = Race::factory()->for($season)->create(['order' => 1]);
    $raceTwo = Race::factory()->for($season)->create(['order' => 2]);

    $this->actingAs($user)
        ->put(route('seasons.races.order', [$season]), [
            'races' => [
                ['id' => $raceOne->id, 'order' => 2],
                ['id' => $raceTwo->id, 'order' => 1],
            ],
        ])
        ->assertRedirect(route('seasons.races.index', [$season]));

    $this->assertEquals(2, $raceOne->fresh()->order);
    $this->assertEquals(1, $raceTwo->fresh()->order);
});

it('shows all races in the selected season on the index page', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    Race::factory(5)->for($season)->create();

    $season->load([
        'poles',
        'winners',
        'races' => fn (HasMany $query) => $query->with('circuit')->orderBy('order'),
    ]);

    $this->actingAs($user)
        ->get(route('seasons.races.index', [$season]))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Races/Index')
                ->has('races.data', 5),
        );
})->skip(message: 'Try to figure out why this fails in the CLI but not in PhpStorm');

it('shows the correct race on the show page', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    Race::factory(5)->for($season)->create();

    $race = $season->races()->first();

    $this->actingAs($user)
        ->get(route('seasons.races.show', [$season, $race]))
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->where('season.year', $season->year)
                ->where('race.name', $race->name),
        );
});

test('a universe owner cannot view the race create page for a started season', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $season->update(['started' => true]);

    actingAs($user)
        ->get(route('seasons.races.create', [$season]))
        ->assertRedirect()
        ->assertSessionHas('error', 'The season has started and can therefore no longer be modified');
});

test('a race cannot be added to a started season', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $season->update(['started' => true]);

    actingAs($user)
        ->post(route('seasons.races.store', [$season]), getRaceCreationData($season))
        ->assertRedirect()
        ->assertSessionHas('error', 'The season has started and can therefore no longer be modified');
});

test('a universe owner can not view the race update page for a started season', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $season->update(['started' => true]);
    $race = Race::factory()->for($season)->create();

    actingAs($user)
        ->get(route('seasons.races.edit', [$season, $race]))
        ->assertRedirect()
        ->assertSessionHas('error', 'The season has started and can therefore no longer be modified');
});

test('a race cannot be updated for a started season', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $season->update(['started' => true]);
    $race = Race::factory()->for($season)->create();

    actingAs($user)
        ->put(route('seasons.races.update', [$season, $race]), [
            'circuit_id' => $race->circuit_id,
            'name' => 'new name',
        ])
        ->assertRedirect()
        ->assertSessionHas('error', 'The season has started and can therefore no longer be modified');
});

test('a universe owner cannot view the reorder race page for a started season', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $season->update(['started' => true]);

    Race::factory(2)->for($season)->create();

    actingAs($user)
        ->get(route('seasons.races.reorder', [$season]))
        ->assertRedirect()
        ->assertSessionHas('error', 'The season has started and can therefore no longer be modified');
});

test('races cannot be reordered after a season has been started', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $season->update(['started' => true]);
    Race::factory(2)->for($season)->create();

    actingAs($user)
        ->put(route('seasons.races.order', [$season]))
        ->assertRedirect()
        ->assertSessionHas('error', 'The season has started and can therefore no longer be modified');
});

test('a universe owner can delete a race from a non-started season', function () {
    $user = User::factory()->create();
    $season = tap(createSeasonForUser($user), fn (Season $season) => Race::factory(5)->for($season)->create());

    actingAs($user)
        ->delete(route('seasons.races.destroy', [$season, $season->races->first()]))
        ->assertNoContent();

    assertCount(4, $season->races()->get());
    assertDatabaseCount('races', 4);
});

test('unauthorized users cannot delete races', function () {
    $season = Season::factory()->create();
    Race::factory(5)->for($season)->create();

    $route = route('seasons.races.destroy', [$season, $season->races->first()]);

    delete($route)
        ->assertForbidden();

    actingAs(User::factory()->create())
        ->delete($route)
        ->assertForbidden();

    assertCount(5, $season->races()->get());
    assertDatabaseCount('races', 5);
});

it('does not delete races from in-progress seasons', function () {
    $user = User::factory()->create();
    $season = tap(createSeasonForUser($user), function (Season $season) {
        Race::factory(5)->for($season)->create();
        $season->update(['started' => true]);
    });

    actingAs($user)
        ->delete(route('seasons.races.destroy', [$season, $season->races->first()]))
        ->assertRedirect(route('index'));

    assertCount(5, $season->races()->get());
    assertDatabaseCount('races', 5);
});

it('does not delete races from completed seasons', function () {
    $user = User::factory()->create();
    $season = tap(createSeasonForUser($user), function (Season $season) {
        Race::factory(5)->for($season)->create();
        $season->update(['completed' => true]);
    });

    actingAs($user)
        ->delete(route('seasons.races.destroy', [$season, $season->races->first()]))
        ->assertRedirect(route('index'));

    assertCount(5, $season->races()->get());
    assertDatabaseCount('races', 5);
});

it('reorders remaining races', function () {
    $user = User::factory()->create();
    $season = tap(createSeasonForUser($user), fn (Season $season) => Race::factory(5)->for($season)->create());

    actingAs($user)
        ->delete(route('seasons.races.destroy', [$season, $season->races->first()]))
        ->assertNoContent();

    foreach ($season->races()->orderBy('order')->get() as $key => $race) {
        assertEquals($key + 1, $race->order);
    }
});

it('correctly determines the next race after reordering races', function () {
    $user = User::factory()->create();
    $season = tap(createSeasonForUser($user), fn (Season $season) => Race::factory(2)->for($season)->create());

    [$firstRace, $secondRace] = Race::all();

    $this->assertEquals($firstRace->id, $season->nextRace()->id);

    $secondRace->update(['order' => 1]);
    $firstRace->update(['order' => 2]);

    $this->assertEquals($secondRace->id, $season->nextRace()->id);
});

function getRaceCreationData(Season $season, ?User $user = null): array
{
    if (! $user) {
        $user = User::factory()->create();
    }

    $circuit = Circuit::factory()->for($user)->create();

    return [
        'circuit_id' => $circuit->id,
        'circuit_variation_id' => CircuitVariation::factory()->for($circuit)->create()->id,
        'climate_id' => Climate::factory()->create()->id,
        'name' => "$season->year Test Grand Prix",
        'order' => 1,
        'race_type' => RaceType::LAP->value,
        'duration' => 50,
    ];
}
