<?php

use Database\Seeders\ClimateSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('climate_conditions', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->foreignId('climate_id')->constrained();
            $table->unsignedInteger('condition');
            $table->unsignedInteger('chance');
            $table->timestamps();
        });

        $climateSeeder = new ClimateSeeder;
        $climateSeeder->run();
    }

    public function down(): void
    {
        Schema::dropIfExists('climate_conditions');
    }
};
