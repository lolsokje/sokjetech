<?php

use App\Models\User;

test('unauthenticated users cannot view the shared database page', function () {
    $this->get(route('database.index'))
        ->assertRedirect(route('index'));
});

it('shows the database index page to authenticated users', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('database.index'))
        ->assertOk();
});
