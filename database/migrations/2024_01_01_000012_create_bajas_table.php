<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla legacy ancha (19 columnas, ~14292 bytes de fila estimados):
        // excede el limite de fila de InnoDB (8126). El servidor de demo corre con
        // innodb_strict_mode=OFF (ver docker-compose.yml) para replicar el esquema original.
        Schema::create('bajas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('token', 255);
            $table->string('quien', 255);
            $table->timestamp('cuando');
            $table->string('tecnico', 255);
            $table->integer('ot');
            $table->string('id_actividad', 255);
            $table->string('nim', 255);
            $table->text('tkl');
            $table->string('motivo', 255);
            $table->string('obs_tecnico', 255);
            $table->string('obs', 500);
            $table->text('direccion');
            $table->string('localidad', 255);
            $table->string('zona_tarea', 255);
            $table->string('cliente', 255);
            $table->string('telefono', 255);
            $table->string('zona', 255);
            $table->date('calendario');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bajas');
    }
};
