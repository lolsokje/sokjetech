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
        Schema::create('engine_seasons', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->foreignId('base_engine_id')->constrained('engines');
            $table->foreignId('season_id')->constrained();
            $table->boolean('rebadge')->default(false);
            $table->boolean('individual_rating')->default(true);
            $table->string('name');
            $table->unsignedInteger('rating')->default(0);
            $table->unsignedInteger('reliability')->default(0);
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
        Schema::dropIfExists('engine_seasons');
    }
};
