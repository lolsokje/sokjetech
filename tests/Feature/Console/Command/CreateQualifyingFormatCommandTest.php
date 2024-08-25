<?php

use Illuminate\Filesystem\Filesystem;
use Mockery\MockInterface;

it('creates the required files', function () {
    $this->mock(Filesystem::class, function (MockInterface $mock) {
        $mock->shouldReceive('exists')
            ->times(3)
            ->andReturnFalse();

        $mock->shouldReceive('put')
            ->times(3)
            ->andReturnTrue();
    });

    $this->artisan('format:make Test')
        ->expectsOutputToContain('Model "Test.php" created')
        ->expectsOutputToContain('Migration "create_tests_table.php" created')
        ->expectsOutputToContain('Factory "TestFactory.php" created')
        ->assertOk();
});

it('does not create files when they already exist', function () {
    $this->mock(Filesystem::class, function (MockInterface $mock) {
        $mock->shouldReceive('exists')
            ->times(3)
            ->andReturnTrue();

        $mock->shouldReceive('put')
            ->never();
    });

    $this->artisan('format:make Test')
        ->expectsOutputToContain('A model with the name "Test.php" already exists')
        ->expectsOutputToContain('A migration with the name "create_tests_table.php" already exists')
        ->expectsOutputToContain('A factory with the name "TestFactory.php" already exists')
        ->assertOk();
});
