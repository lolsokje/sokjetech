<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('circuit_variations', function (Blueprint $table) {
            $table->unsignedFloat('engine_multiplier')->default(1.0)->after('base_laptime');
            $table->unsignedFloat('team_multiplier')->default(1.0)->after('base_laptime');
        });
    }

    public function down(): void
    {
        Schema::table('circuit_variations', function (Blueprint $table) {
            $table->dropColumn('engine_multiplier');
            $table->dropColumn('team_multiplier');
        });
    }
};
