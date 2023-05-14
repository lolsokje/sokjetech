<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('climates', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->string('short_name');
            $table->string('long_name');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('climates');
    }
};
