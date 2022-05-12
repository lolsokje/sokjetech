<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('single_sessions', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->unsignedInteger('runs_per_session');
            $table->unsignedInteger('min_rng');
            $table->unsignedInteger('max_rng');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('single_sessions');
    }
};
