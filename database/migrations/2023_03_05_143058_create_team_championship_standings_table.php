<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('team_championship_standings', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->foreignId('entrant_id')->constrained();
            $table->foreignId('season_id')->constrained();
            $table->unsignedInteger('position');
            $table->unsignedInteger('points');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_championship_standings');
    }
};
