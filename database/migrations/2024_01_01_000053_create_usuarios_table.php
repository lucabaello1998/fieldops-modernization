<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla legacy ancha (16 columnas, ~12472 bytes de fila estimados):
        // excede el limite de fila de InnoDB (8126). El servidor de demo corre con
        // innodb_strict_mode=OFF (ver docker-compose.yml) para replicar el esquema original.
        Schema::create('usuarios', function (Blueprint $table) {
            $table->increments('id');
            $table->string('token', 255);
            $table->date('ingreso');
            $table->string('activo', 50);
            $table->string('nombre', 255);
            $table->string('apellido', 255);
            $table->string('mail', 255);
            $table->string('usuario', 255);
            $table->string('pass', 255);
            $table->string('tipo_us', 255);
            $table->string('zona', 255);
            $table->string('abreviatura', 255);
            $table->string('icono', 255);
            $table->string('tema', 255);
            $table->string('fuente', 255);
            $table->text('firebase');

            $table->index('usuario');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
