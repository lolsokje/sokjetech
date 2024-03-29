<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntrantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entrants', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->foreignId('season_id')->constrained();
            $table->foreignId('team_id')->constrained();
            $table->foreignId('engine_id')->nullable()->constrained('engine_seasons');
            $table->string('full_name');
            $table->string('short_name');
            $table->string('team_principal');
            $table->string('primary_colour');
            $table->string('secondary_colour');
            $table->string('country');
            $table->boolean('active')->default(true);
            $table->unsignedInteger('rating')->nullable()->default(0);
            $table->unsignedInteger('reliability')->nullable()->default(0);
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
        Schema::dropIfExists('entrants');
    }
}
