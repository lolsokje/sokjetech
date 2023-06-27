<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('races', function (Blueprint $table) {
            $table->string('distance_type')->nullable()->default(null)->after('race_type');
            $table->unsignedInteger('duration')->after('race_type');
        });
    }

    public function down(): void
    {
        Schema::table('races', function (Blueprint $table) {
            $table->dropColumn('distance_type');
            $table->dropColumn('duration');
        });
    }
};
