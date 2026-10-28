<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('track', function (Blueprint $table) {
            $table->increments('id');
            $table->string('token', 255);
            $table->string('latitud', 255);
            $table->string('longitud', 255);
            $table->string('tiempo', 255);
            $table->string('velocidad', 255);
            $table->string('estado', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('track');
    }
};
