<?php

use App\Enums\BugStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('bugs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('type');
            $table->string('summary');
            $table->longText('details');
            $table->string('status')->default(BugStatus::NEW->value);
            $table->longText('admin_remarks')->nullable();
            $table->string('app_version', 10)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bugs');
    }
};
