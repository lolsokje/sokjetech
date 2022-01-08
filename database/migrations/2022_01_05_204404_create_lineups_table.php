<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLineupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lineups', function (Blueprint $table) {
            $table->foreignUuid('id')->primary();
            $table->foreignUuid('season_id')->nullable()->constrained();
            $table->foreignUuid('driver_id')->nullable()->constrained();
            $table->foreignUuid('entrant_id')->nullable()->constrained();
            $table->unsignedInteger('number');
            $table->unsignedInteger('rating')->default(0);
            $table->unsignedInteger('reliability')->default(0);
            $table->boolean('active')->default(true);
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
        Schema::dropIfExists('lineups');
    }
}
