<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('reliability_reasons', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->foreignId('season_id')->constrained();
            $table->unsignedInteger('type');
            $table->string('reason');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reliability_reasons');
    }
};
