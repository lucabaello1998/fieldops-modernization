<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla legacy ancha (24 columnas, ~20445 bytes de fila estimados):
        // excede el limite de fila de InnoDB (8126). El servidor de demo corre con
        // innodb_strict_mode=OFF (ver docker-compose.yml) para replicar el esquema original.
        Schema::create('precontacto', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('orden');
            $table->string('token', 255);
            $table->string('estado', 255);
            $table->string('quien', 255);
            $table->dateTime('cuando');
            $table->string('tecnico', 255);
            $table->string('id_actividad', 255);
            $table->string('ot', 255);
            $table->string('direccion', 255);
            $table->string('localidad', 255);
            $table->string('zona', 255);
            $table->string('fecha', 255);
            $table->string('intervalo', 255);
            $table->string('actividad', 255);
            $table->string('codigo', 255);
            $table->string('cantidad_tv', 255);
            $table->string('cliente', 255);
            $table->string('telefono', 255);
            $table->string('telefono_alternativo', 255);
            $table->string('nim', 255);
            $table->string('motivo_asignacion', 255);
            $table->string('zona_recurso', 255);
            $table->text('obs');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('precontacto');
    }
};
