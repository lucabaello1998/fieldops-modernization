<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('no_conformidades', function (Blueprint $table) {
            $table->increments('id');
            $table->string('quien', 255);
            $table->timestamp('cuando');
            $table->string('tecnico', 255);
            $table->date('fecha');
            $table->string('supervisor', 255);
            $table->text('problema');
            $table->string('id_nc', 255);
            $table->string('imagenpri', 255);
            $table->string('imagenseg', 255);
            $table->string('imagenter', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('no_conformidades');
    }
};
