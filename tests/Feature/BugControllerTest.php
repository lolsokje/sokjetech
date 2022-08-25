<?php

use App\Enums\BugStatus;
use App\Models\Bug;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;
use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotNull;

test('unauthenticated users cannot see the bug index page', function () {
    get(route('bugs.index'))
        ->assertRedirect(route('index'));
});

test('unauthenticated users cannot see the bug create page', function () {
    get(route('bugs.create'))
        ->assertRedirect(route('index'));
});

test('unauthenticated users cannot create bugs', function () {
    post(route('bugs.store'))
        ->assertRedirect(route('index'));
});

test('an authenticated user can view the bug index page', function () {
    actingAs(User::factory()->create())
        ->get(route('bugs.index'))
        ->assertOk();
});

test('an authenticated user can view the bug create page', function () {
    actingAs(User::factory()->create())
        ->get(route('bugs.create'))
        ->assertOk();
});

test('an authenticated user can submit a bug', function () {
    actingAs(User::factory()->create())
        ->post(route('bugs.store'), [
            'type' => 'Type',
            'summary' => 'Summary',
            'details' => 'Details',
        ])
        ->assertRedirect(route('bugs.index'));

    assertDatabaseCount('bugs', 1);

    $bug = Bug::query()->first();

    assertEquals('Type', $bug->type);
    assertEquals('Summary', $bug->summary);
    assertEquals('Details', $bug->details);
    assertEquals(BugStatus::NEW, $bug->status);
});

it('checks the required fields are provided when reporting a bug', function () {
    actingAs(User::factory()->create())
        ->post(route('bugs.store'))
        ->assertInvalid([
            'type' => 'required',
            'summary' => 'required',
            'details' => 'required',
        ]);

    assertDatabaseCount('bugs', 0);
});

it('paginates bugs on the bug index page', function () {
    Bug::factory(40)->create();

    actingAs(User::factory()->create())
        ->get(route('bugs.index'))
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Bugs/Index')
                ->has('bugs', 20)
                ->has('links', 4),
        );
});

it('it only shows open bugs when requesting open bugs', function () {
    collect(BugStatus::cases())->each(fn (BugStatus $status) => Bug::factory()->create(['status' => $status]));

    actingAs(User::factory()->create())
        ->get(route('bugs.index', ['only' => 'open']))
        ->assertInertia(
            fn (Assert $assert) => $assert
                ->component('Bugs/Index')
                ->has('bugs', 6),
        );
});

it('only shows closed bugs when requesting closed bugs', function () {
    collect(BugStatus::cases())->each(fn (BugStatus $status) => Bug::factory()->create(['status' => $status]));

    actingAs(User::factory()->create())
        ->get(route('bugs.index', ['only' => 'closed']))
        ->assertInertia(
            fn (Assert $assert) => $assert
                ->component('Bugs/Index')
                ->has('bugs', 2),
        );
});

test('authenticated users can view the bug edit page', function () {
    $bug = Bug::factory()->create();

    get(route('bugs.edit', $bug))
        ->assertRedirect(route('index'));

    actingAs(User::factory()->create())
        ->get(route('bugs.edit', $bug))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Bugs/Edit')
            ->has('bug', fn (Assert $prop) => $prop
                ->where('summary', $bug->summary)
                ->where('details', $bug->details)
                ->etc(),
            ),
        );

    actingAs(User::factory()->create(['is_admin' => true]))
        ->get(route('bugs.edit', $bug))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Bugs/Edit')
            ->has('bug', fn (Assert $prop) => $prop
                ->where('summary', $bug->summary)
                ->where('details', $bug->details)
                ->etc(),
            ),
        );
});

test('an admin can update the status of a bug', function () {
    $bug = Bug::factory()->create();

    actingAs(User::factory()->create(['is_admin' => true]))
        ->put(route('bugs.update', $bug), [
            'status' => BugStatus::CONFIRMED->value,
        ])
        ->assertRedirect(route('bugs.edit', $bug));

    assertEquals(BugStatus::CONFIRMED, $bug->fresh()->status);
});

test('unauthorised users cannot update the status of a bug', function () {
    $bug = Bug::factory()->create();

    put(route('bugs.update', $bug), [
        'status' => BugStatus::CONFIRMED->value,
    ])
        ->assertRedirect(route('index'));

    actingAs(User::factory()->create())
        ->put(route('bugs.update', $bug), [
            'status' => BugStatus::CONFIRMED->value,
        ])
        ->assertRedirect(route('index'));

    assertEquals(BugStatus::NEW, $bug->fresh()->status);
});

it('validates the new status', function () {
    $bug = Bug::factory()->create();

    actingAs(User::factory()->create(['is_admin' => true]))
        ->put(route('bugs.update', $bug), [
            'status' => 'invalid',
        ])
        ->assertInvalid('status');

    assertEquals(BugStatus::NEW, $bug->fresh()->status);
});

test('an admin can add remarks to a bug', function () {
    $bug = Bug::factory()->create();

    actingAs(User::factory()->create(['is_admin' => true]))
        ->put(route('bugs.update', $bug), [
            'status' => $bug->status->value,
            'admin_remarks' => 'remark',
        ])
        ->assertRedirect(route('bugs.edit', $bug));

    assertEquals('remark', $bug->fresh()->admin_remarks);
});

it('automatically stores the current app version when creating a bug', function () {
    actingAs(User::factory()->create())
        ->post(route('bugs.store'), [
            'type' => 'Type',
            'summary' => 'Summary',
            'details' => 'Details',
        ])
        ->assertRedirect(route('bugs.index'));

    assertNotNull(Bug::first()->app_version);
});
