<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('races', function (Blueprint $table) {
            $table->unsignedInteger('qualifying_run')->after('duration')->default(1);
            $table->unsignedInteger('qualifying_session')->after('duration')->default(1);
        });
    }

    public function down(): void
    {
        Schema::table('races', function (Blueprint $table) {
            $table->dropColumn('qualifying_run');
            $table->dropColumn('qualifying_session');
        });
    }
};
