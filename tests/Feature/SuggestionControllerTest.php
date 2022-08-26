<?php


use App\Enums\SuggestionStatus;
use App\Models\Suggestion;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\put;
use function PHPUnit\Framework\assertEquals;

test('unauthenticated users cannot see the suggestion index page', function () {
    $this->get(route('suggestions.index'))
        ->assertRedirect(route('index'));
});

test('unauthenticated users cannot see the suggestion create page', function () {
    $this->get(route('suggestions.create'))
        ->assertRedirect(route('index'));
});

test('unauthenticated users cannot create suggestions', function () {
    $this->post(route('suggestions.store'))
        ->assertRedirect(route('index'));
});

test('an authenticated user can view the suggestions index page', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('suggestions.index'))
        ->assertOk();
});

test('an authenticated user can view the suggestion create page', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('suggestions.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $assert) => $assert
            ->component('Suggestions/Create'));
});

test('an authenticated user can submit a suggestion', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('suggestions.store'), [
            'type' => 'Type',
            'summary' => 'Summary',
            'details' => 'Details',
        ])
        ->assertRedirect(route('suggestions.index'));

    assertDatabaseCount('suggestions', 1);

    $suggestion = Suggestion::query()->first();

    assertEquals('Type', $suggestion->type);
    assertEquals('Summary', $suggestion->summary);
    assertEquals('Details', $suggestion->details);
    assertEquals(SuggestionStatus::NEW, $suggestion->status);
});

it('checks the required fields are provided when submitting a suggestion', function () {
    $this->actingAs(User::factory()->create())
        ->post(route('suggestions.store'))
        ->assertInvalid([
            'type' => 'required',
            'summary' => 'required',
            'details' => 'required',
        ]);

    assertDatabaseCount('suggestions', 0);
});

it('paginates suggestions on the index page', function () {
    Suggestion::factory(40)->create();

    $this->actingAs(User::factory()->create())
        ->get(route('suggestions.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $assert) => $assert
            ->component('Suggestions/Index')
            ->has('suggestions', 20)
            ->has('links', 4));
});

it('only shows open suggestions when requesting open suggestions', function () {
    collect(SuggestionStatus::cases())->each(fn (SuggestionStatus $status) => Suggestion::factory()->create([
        'status' => $status,
    ]));

    $this->actingAs(User::factory()->create())
        ->get(route('suggestions.index', ['only' => 'open']))
        ->assertOk()
        ->assertInertia(fn (Assert $assert) => $assert
            ->component('Suggestions/Index')
            ->has('suggestions', count(SuggestionStatus::openCases())));
});

it('only shows closed suggestions when requesting closed suggestions', function () {
    collect(SuggestionStatus::cases())->each(fn (SuggestionStatus $status) => Suggestion::factory()->create([
        'status' => $status,
    ]));

    $this->actingAs(User::factory()->create())
        ->get(route('suggestions.index', ['only' => 'closed']))
        ->assertOk()
        ->assertInertia(fn (Assert $assert) => $assert
            ->component('Suggestions/Index')
            ->has('suggestions', count(SuggestionStatus::closedCases())));
});

test('authenticated users can view the suggestion edit page', function () {
    $suggestion = Suggestion::factory()->create();

    $this->get(route('suggestions.edit', $suggestion))
        ->assertRedirect(route('index'));

    $this->actingAs(User::factory()->create())
        ->get(route('suggestions.edit', $suggestion))
        ->assertOk()
        ->assertInertia(fn (Assert $assert) => $assert
            ->component('Suggestions/Edit')
            ->has('suggestion', fn (Assert $prop) => $prop
                ->where('summary', $suggestion->summary)
                ->where('details', $suggestion->details)
                ->etc(),
            ),
        );
});

test('an admin can update the status of a suggestion', function () {
    $suggestion = Suggestion::factory()->create();

    $this->actingAs(User::factory()->create(['is_admin' => true]))
        ->put(route('suggestions.update', $suggestion), [
            'status' => SuggestionStatus::WILL_IMPLEMENT->value,
        ])
        ->assertRedirect(route('suggestions.edit', $suggestion));

    assertEquals(SuggestionStatus::WILL_IMPLEMENT, $suggestion->fresh()->status);
});

test('unauthorised users cannot update the status of a suggestion', function () {
    $suggestion = Suggestion::factory()->create();

    put(route('suggestions.update', $suggestion), [
        'status' => SuggestionStatus::WILL_IMPLEMENT->value,
    ])
        ->assertRedirect(route('index'));

    actingAs(User::factory()->create())
        ->put(route('suggestions.update', $suggestion), [
            'status' => SuggestionStatus::WILL_IMPLEMENT->value,
        ])
        ->assertRedirect(route('index'));

    assertEquals(SuggestionStatus::NEW, $suggestion->fresh()->status);
});

it('validates the new status', function () {
    $suggestion = Suggestion::factory()->create();

    actingAs(User::factory()->create(['is_admin' => true]))
        ->put(route('suggestions.update', $suggestion), [
            'status' => 'invalid',
        ])
        ->assertInvalid('status');

    assertEquals(SuggestionStatus::NEW, $suggestion->fresh()->status);
});

test('an admin can add remarks to a suggestion', function () {
    $suggestion = Suggestion::factory()->create();

    $this->actingAs(User::factory()->create(['is_admin' => true]))
        ->put(route('suggestions.update', $suggestion), [
            'status' => $suggestion->status->value,
            'admin_remarks' => 'remark',
        ])
        ->assertRedirect(route('suggestions.edit', $suggestion));

    assertEquals('remark', $suggestion->fresh()->admin_remarks);
});
