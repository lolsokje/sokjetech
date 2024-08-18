<?php

use App\Contracts\Development\HasRatingHistory;
use App\Contracts\Development\IsDevelopmentEntity;
use App\Enums\Season\Development\DevelopmentType;
use App\Models\DriverDevelopmentHistory;
use App\Models\Race;
use App\Models\Racer;
use App\Models\Season;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

test('universe owners can store development', function (DevelopmentType $type, string $component, Factory $factory) {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    Race::factory()->for($season)->create();

    $models = $factory->for($season)->create();
    $ratingOne = $models[0]->getComponentRating($component);
    $ratingTwo = $models[1]->getComponentRating($component);

    $entities = $models->map(
        fn(IsDevelopmentEntity&HasRatingHistory $model) => generateDevelopmentEntity($model, $component, 10),
    )->toArray();

    $this->actingAs($user)
        ->post(route('seasons.development.store', [$season, $type, $component]), [
            'entities' => $entities,
        ])
        ->assertRedirect(route('seasons.development.show', [$season, $type, $component]))
        ->assertSessionMissing('error')
        ->assertSessionHas('notice', 'New ratings have been saved');

    $models->each(fn(Model $model) => $model->refresh());

    $this->assertEquals($ratingOne + 10, $models[0]->getComponentRating($component));
    $this->assertEquals($ratingTwo + 10, $models[1]->getComponentRating($component));
})->with('development options');

test('development cannot be stored when there is an active race', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    Race::factory()->for($season)->create(['qualifying_started' => true]);

    $racer = Racer::factory()->for($season)->create();

    $this->actingAs($user)
        ->post(route('seasons.development.store', [$season, DevelopmentType::DRIVER, 'rating']), [
            'entities' => [generateDevelopmentEntity($racer, 'rating', 0)],
        ])
        ->assertRedirectToRoute('seasons.development.show', [$season, DevelopmentType::DRIVER, 'rating'])
        ->assertSessionHas(
            'error',
            "There's currently an active race, development can't be performed until that race has been completed",
        )
        ->assertSessionMissing('notice');
});

test('development cannot be stored when there is no next race', function () {
    $user = User::factory()->create();
    $season = createSeasonForUser($user);
    $racer = Racer::factory()->for($season)->create();

    $this->actingAs($user)
        ->post(route('seasons.development.store', [$season, DevelopmentType::DRIVER, 'rating']), [
            'entities' => [generateDevelopmentEntity($racer, 'rating', 0)],
        ])
        ->assertRedirectToRoute('seasons.development.show', [$season, DevelopmentType::DRIVER, 'rating'])
        ->assertSessionHas('error', 'No next race available for this season')
        ->assertSessionMissing('notice');
});

test('unauthorised users cannot store development', function (
    DevelopmentType $type,
    string $component,
    Factory $factory,
    ?User $user,
) {
    $season = Season::factory()->create();
    $entities = $factory->for($season)->create();

    $entities = $entities->map(
        fn(HasRatingHistory&IsDevelopmentEntity $entity) => generateDevelopmentEntity($entity, $component, 10),
    )->toArray();

    if ($user) {
        $this->actingAs($user);
    }

    $this->post(route('seasons.development.store', [$season, $type, $component]), [
        'entities' => $entities,
    ])
        ->assertForbidden();
})->with('development options')->with([
    [null],
    [fn() => User::factory()->create()],
]);

test('all new ratings must be at least 0', function () {
    $season = Season::factory()->create();
    Race::factory()->for($season)->create();
    $racer = Racer::factory()->for($season)->create(['rating' => 10]);

    $entity = generateDevelopmentEntity($racer, 'rating', -11);

    $this->actingAs($season->universe->user)
        ->from(route('seasons.development.show', [$season, DevelopmentType::DRIVER, 'rating']))
        ->post(route('seasons.development.store', [$season, DevelopmentType::DRIVER, 'rating']), [
            'entities' => [$entity],
        ])
        ->assertInvalid('entities.0.new')
        ->assertRedirectToRoute('seasons.development.show', [$season, DevelopmentType::DRIVER, 'rating']);

    $this->assertCount(0, DriverDevelopmentHistory::query()->get());
});
