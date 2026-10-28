<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla legacy ancha (15 columnas, ~9268 bytes de fila estimados):
        // excede el limite de fila de InnoDB (8126). El servidor de demo corre con
        // innodb_strict_mode=OFF (ver docker-compose.yml) para replicar el esquema original.
        Schema::create('tecnicosatc', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 255);
            $table->string('apellido', 255);
            $table->integer('num_empleado');
            $table->string('mail', 255);
            $table->string('usuario', 255);
            $table->string('pass', 255);
            $table->string('tel', 15);
            $table->integer('dni');
            $table->string('tarea', 255);
            $table->string('enlace', 255);
            $table->string('operativo', 255);
            $table->string('color', 255);
            $table->date('inicio');
            $table->date('fin');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tecnicosatc');
    }
};
