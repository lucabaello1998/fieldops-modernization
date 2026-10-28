<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla legacy ancha (15 columnas, ~10383 bytes de fila estimados):
        // excede el limite de fila de InnoDB (8126). El servidor de demo corre con
        // innodb_strict_mode=OFF (ver docker-compose.yml) para replicar el esquema original.
        Schema::create('reportevl', function (Blueprint $table) {
            $table->increments('id');
            $table->date('fecha');
            $table->string('acronimo', 255);
            $table->string('direccion', 255);
            $table->string('callealtura', 255);
            $table->string('rotulada', 255);
            $table->string('tipo', 255);
            $table->integer('cant_drop');
            $table->string('latitud', 20);
            $table->string('longitud', 20);
            $table->string('fleje', 255);
            $table->string('trabas', 255);
            $table->string('splitter_conectores', 255);
            $table->string('prioridad', 255);
            $table->string('obs', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportevl');
    }
};
