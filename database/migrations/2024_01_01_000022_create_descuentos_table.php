<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('descuentos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('identificador', 255);
            $table->string('tecnico', 255);
            $table->date('fecha');
            $table->integer('ot');
            $table->string('falla', 255);
            $table->integer('monto');
            $table->string('obs', 255);
            $table->string('imagenpri', 255);
            $table->string('imagenseg', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('descuentos');
    }
};
