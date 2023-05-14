<?php

use App\Models\Climate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('races', function (Blueprint $table) {
            $table->foreignId('climate_id')->after('circuit_id')->default(Climate::first()->id)->constrained();
        });
    }

    public function down(): void
    {
        Schema::table('races', function (Blueprint $table) {
            $table->dropConstrainedForeignId('climate_id');
        });
    }
};
