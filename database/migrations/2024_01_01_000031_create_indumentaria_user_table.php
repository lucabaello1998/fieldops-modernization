<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indumentaria_user', function (Blueprint $table) {
            $table->increments('id');
            $table->string('indumentaria', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indumentaria_user');
    }
};
