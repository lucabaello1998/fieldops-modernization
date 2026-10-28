<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla legacy ancha (17 columnas, ~8728 bytes de fila estimados):
        // excede el limite de fila de InnoDB (8126). El servidor de demo corre con
        // innodb_strict_mode=OFF (ver docker-compose.yml) para replicar el esquema original.
        Schema::create('corpo_tareas', function (Blueprint $table) {
            $table->increments('id');
            $table->date('F_Asignado');
            $table->string('estado', 20);
            $table->integer('orden');
            $table->integer('enlace');
            $table->string('asignado_por', 100);
            $table->string('cliente', 100);
            $table->string('referente', 100);
            $table->string('domicilio', 150);
            $table->string('celular', 25);
            $table->string('email_contacto', 150);
            $table->string('imagen_1', 255);
            $table->string('imagen_2', 255);
            $table->string('imagen_3', 255);
            $table->string('imagen_4', 255);
            $table->string('imagen_5', 255);
            $table->string('imagen_6', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corpo_tareas');
    }
};
