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
        Schema::create('point_systems', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->foreignId('season_id')->constrained();
            $table->boolean('fastest_lap_point_awarded')->default(false);
            $table->boolean('pole_position_point_awarded')->default(false);
            $table->unsignedInteger('fastest_lap_point_amount')->nullable();
            $table->unsignedInteger('pole_position_point_amount')->nullable();
            $table->string('fastest_lap_determination')->nullable();
            $table->unsignedInteger('fastest_lap_min_rng')->nullable();
            $table->unsignedInteger('fastest_lap_max_rng')->nullable();
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
        Schema::dropIfExists('point_systems');
    }
};
