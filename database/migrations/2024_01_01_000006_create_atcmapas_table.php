<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla legacy ancha (12 columnas, ~9200 bytes de fila estimados):
        // excede el limite de fila de InnoDB (8126). El servidor de demo corre con
        // innodb_strict_mode=OFF (ver docker-compose.yml) para replicar el esquema original.
        Schema::create('atcmapas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 255);
            $table->date('fecha');
            $table->string('zona', 255);
            $table->string('partido', 255);
            $table->string('localidad', 255);
            $table->integer('orden');
            $table->string('km', 255);
            $table->string('tarea', 255);
            $table->string('estado', 255);
            $table->string('enlace', 255);
            $table->string('color', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atcmapas');
    }
};
