<?php

use App\Models\Entrant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('entrants', function (Blueprint $table) {
            $table->string('accent_colour')->after('secondary_colour');
        });

        Entrant::all()->each(fn (Entrant $entrant) => $entrant->update(['accent_colour' => $entrant->primary_colour]));
    }

    public function down()
    {
        Schema::table('entrants', function (Blueprint $table) {
            $table->dropColumn('accent_colour');
        });
    }
};
