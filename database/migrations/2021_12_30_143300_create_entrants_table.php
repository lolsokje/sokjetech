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
            $table->uuid('id')->primary();
            $table->foreignUuid('season_id')->constrained();
            $table->foreignUuid('team_id')->constrained();
            $table->foreignUuid('engine_id')->nullable()->constrained();
            $table->string('full_name');
            $table->string('short_name');
            $table->string('team_principal');
            $table->string('primary_colour');
            $table->string('secondary_colour');
            $table->string('country');
            $table->boolean('active')->default(true);
            $table->unsignedInteger('rating')->nullable()->default(null);
            $table->unsignedInteger('reliability')->nullable()->default(null);
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
