<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('driver_championship_standings', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->foreignId('racer_id')->constrained();
            $table->foreignId('season_id')->constrained();
            $table->unsignedInteger('position');
            $table->unsignedInteger('points');
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('driver_championship_standings');
    }
};
