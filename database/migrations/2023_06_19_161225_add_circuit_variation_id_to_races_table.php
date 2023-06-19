<?php

use App\Models\Race;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('races', function (Blueprint $table) {
            $table->foreignId('circuit_variation_id')->after('circuit_id')->nullable()->constrained();
        });

        $races = Race::query()
            ->with('circuit.variations')
            ->get();

        foreach ($races as $race) {
            $variation = $race->circuit->variations->first();
            $race->update(['circuit_variation_id' => $variation->id]);
        }

        Schema::disableForeignKeyConstraints();
        Schema::table('races', function (Blueprint $table) {
            $table->foreignId('circuit_variation_id')->change()->nullable(false);
        });
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::table('races', function (Blueprint $table) {
            $table->dropColumn('circuit_variation_id');
        });
    }
};
