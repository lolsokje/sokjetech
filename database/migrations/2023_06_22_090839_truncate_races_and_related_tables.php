<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private array $tables = [
        'qualifying_results',
        'race_results',
        'races',
        'driver_championship_standings',
        'team_championship_standings',
    ];

    public function up(): void
    {
        Schema::disableForeignKeyConstraints();
        foreach ($this->tables as $table) {
            DB::table($table)->truncate();
        }
        Schema::enableForeignKeyConstraints();

        DB::table('seasons')->update(['started' => false, 'completed' => false]);
    }
};
