<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('circuit_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('circuit_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('length');
            $table->unsignedInteger('base_laptime');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('circuit_variations');
    }
};
