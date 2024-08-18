<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('driver_development_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_id')->constrained()->cascadeOnDelete();
            $table->foreignId('racer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('race_id')->constrained()->cascadeOnDelete();
            $table->string('component');
            $table->unsignedInteger('initial')->default(0);
            $table->integer('development')->default(0);
            $table->timestamps();
        });

        Schema::create('team_development_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_id')->constrained()->cascadeOnDelete();
            $table->foreignId('entrant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('race_id')->constrained()->cascadeOnDelete();
            $table->string('component');
            $table->unsignedInteger('initial')->default(0);
            $table->integer('development')->default(0);
            $table->timestamps();
        });

        Schema::create('engine_development_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_id')->constrained()->cascadeOnDelete();
            $table->foreignId('engine_season_id')->constrained()->cascadeOnDelete();
            $table->foreignId('race_id')->constrained()->cascadeOnDelete();
            $table->string('component');
            $table->unsignedInteger('initial')->default(0);
            $table->integer('development')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_development_histories');
        Schema::dropIfExists('team_development_histories');
        Schema::dropIfExists('engine_development_histories');
    }
};
