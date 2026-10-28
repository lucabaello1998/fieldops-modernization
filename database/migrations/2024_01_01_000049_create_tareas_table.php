<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla legacy ancha (23 columnas, ~16378 bytes de fila estimados):
        // excede el limite de fila de InnoDB (8126). El servidor de demo corre con
        // innodb_strict_mode=OFF (ver docker-compose.yml) para replicar el esquema original.
        Schema::create('tareas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('token', 255);
            $table->string('quien', 255);
            $table->timestamp('cuando');
            $table->string('titulo', 255);
            $table->integer('prioridad');
            $table->string('tarea', 255);
            $table->string('abreviado', 255);
            $table->string('sub_tarea', 255);
            $table->string('sub_estado', 255);
            $table->text('descripcion');
            $table->string('archivo_uno', 255);
            $table->string('nom_archivo_uno', 255);
            $table->string('archivo_dos', 255);
            $table->string('nom_archivo_dos', 255);
            $table->string('archivo_tres', 255);
            $table->string('nom_archivo_tres', 255);
            $table->date('inicio');
            $table->date('fin');
            $table->string('a_quien', 255);
            $table->text('mensaje');
            $table->string('color', 255);
            $table->string('estado', 255);

            $table->index('token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tareas');
    }
};
