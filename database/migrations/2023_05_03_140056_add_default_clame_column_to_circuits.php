<?php

use App\Models\Climate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('circuits', function (Blueprint $table) {
            $table->foreignId('default_climate_id')->default(Climate::first()->id)->constrained('climates');
        });
    }

    public function down(): void
    {
        Schema::table('circuits', function (Blueprint $table) {
            $table->dropConstrainedForeignId('default_climate_id');
        });
    }
};
