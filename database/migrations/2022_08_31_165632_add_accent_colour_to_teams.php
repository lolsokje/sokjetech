<?php

use App\Models\Team;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('accent_colour')->after('secondary_colour');
        });

        Team::all()->each(fn (Team $team) => $team->update(['accent_colour' => $team->primary_colour]));
    }

    public function down()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('accent_colour');
        });
    }
};
