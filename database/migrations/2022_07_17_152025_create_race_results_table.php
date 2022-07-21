<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('race_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('race_id')->constrained();
            $table->foreignId('season_id')->constrained();
            $table->foreignId('racer_id')->constrained();
            $table->foreignId('entrant_id')->constrained();
            $table->unsignedInteger('starting_position');
            $table->unsignedInteger('position')->nullable()->default(null);
            $table->unsignedInteger('driver_rating')->nullable();
            $table->unsignedInteger('team_rating')->nullable();
            $table->unsignedInteger('engine_rating')->nullable();
            $table->unsignedInteger('starting_bonus')->nullable();
            $table->json('stints')->nullable()->default(null);
            $table->unsignedInteger('fastest_lap_roll')->nullable()->default(null);
            $table->string('dnf')->nullable()->default(null);
            $table->boolean('fastest_lap')->default(false);
            $table->unsignedInteger('points')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('race_results');
    }
};
