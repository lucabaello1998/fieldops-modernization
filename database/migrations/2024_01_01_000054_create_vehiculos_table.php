<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('patente', 255);
            $table->string('auto', 255);
            $table->string('color', 255);
            $table->string('vigente', 255);
            $table->date('vtv');
            $table->string('vigenteseg', 255);
            $table->date('seguro');
            $table->string('archivo', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};
