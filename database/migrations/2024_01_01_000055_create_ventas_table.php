<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla legacy ancha (19 columnas, ~14323 bytes de fila estimados):
        // excede el limite de fila de InnoDB (8126). El servidor de demo corre con
        // innodb_strict_mode=OFF (ver docker-compose.yml) para replicar el esquema original.
        Schema::create('ventas', function (Blueprint $table) {
            $table->increments('id');
            $table->text('title');
            $table->string('nombre', 255);
            $table->string('apellido', 255);
            $table->string('dni', 255);
            $table->string('contacto', 255);
            $table->string('descripcion', 255);
            $table->string('entrecallea', 255);
            $table->string('entrecalleb', 255);
            $table->string('provincia', 255);
            $table->string('localidad', 255);
            $table->date('fecha');
            $table->dateTime('start');
            $table->dateTime('end');
            $table->string('turno', 255);
            $table->string('instalacion', 255);
            $table->string('obs', 255);
            $table->string('color', 255);
            $table->string('textColor', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
