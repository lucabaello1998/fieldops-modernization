<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla legacy ancha (21 columnas, ~13335 bytes de fila estimados):
        // excede el limite de fila de InnoDB (8126). El servidor de demo corre con
        // innodb_strict_mode=OFF (ver docker-compose.yml) para replicar el esquema original.
        Schema::create('calendario', function (Blueprint $table) {
            $table->increments('id');
            $table->string('token', 255);
            $table->string('quien', 255);
            $table->timestamp('cuando');
            $table->string('a_quien', 255);
            $table->string('titulo', 255);
            $table->text('contenido');
            $table->string('color', 255);
            $table->date('inicio');
            $table->date('fin');
            $table->string('tecnico', 255);
            $table->string('tarea', 255);
            $table->string('id_tarea', 255);
            $table->string('ot_tarea', 255);
            $table->text('id_auditoria');
            $table->string('estado', 255);
            $table->text('obs');
            $table->string('tomado_por', 255);
            $table->string('archivo_uno', 255);
            $table->string('archivo_dos', 255);
            $table->text('obs_supervisor');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendario');
    }
};
