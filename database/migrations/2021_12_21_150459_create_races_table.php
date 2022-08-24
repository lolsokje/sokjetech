<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('races', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->foreignId('season_id')->constrained();
            $table->foreignId('circuit_id')->constrained();
            $table->string('name');
            $table->unsignedInteger('order')->nullable();
            $table->boolean('qualifying_started')->default(false);
            $table->boolean('qualifying_completed')->default(false);
            $table->boolean('started')->default(false);
            $table->boolean('completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->json('qualifying_details')->nullable()->comment('a column to keep track of things like current qualifying session/run, etc');
            $table->json('race_details')->nullable()->comment('a column to keep track of things like completed race stints, FL awarded, etc');
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
        Schema::dropIfExists('races');
    }
}
