<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimiento_tecnico', function (Blueprint $table) {
            $table->increments('id');
            $table->string('token', 255);
            $table->string('quien', 255);
            $table->timestamp('cuando');
            $table->string('movimiento', 255);
            $table->string('zona', 255);
            $table->text('obs');
            $table->string('latitud', 255);
            $table->string('longitud', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimiento_tecnico');
    }
};
