<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('races', function (Blueprint $table) {
            $table->unsignedInteger('current_lap')->default(0)->after('duration');
        });
    }

    public function down(): void
    {
        Schema::table('races', function (Blueprint $table) {
            //
        });
    }
};
