<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('stints');
    }

    public function down(): void
    {
        Schema::create('stints', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->foreignId('race_id')->constrained();
            $table->unsignedInteger('order');
            $table->integer('min_rng');
            $table->integer('max_rng');
            $table->boolean('reliability')->default(false);
            $table->boolean('use_team_rating')->default(false);
            $table->boolean('use_driver_rating')->default(false);
            $table->boolean('use_engine_rating')->default(false);
            $table->timestamps();
        });
    }
};
