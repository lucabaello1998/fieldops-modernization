<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla legacy ancha (25 columnas, ~23490 bytes de fila estimados):
        // excede el limite de fila de InnoDB (8126). El servidor de demo corre con
        // innodb_strict_mode=OFF (ver docker-compose.yml) para replicar el esquema original.
        Schema::create('atcreportesgpon', function (Blueprint $table) {
            $table->increments('id');
            $table->date('fecha');
            $table->string('hora', 255);
            $table->string('empleado', 255);
            $table->string('num_empleado', 255);
            $table->string('partido', 255);
            $table->string('localidad', 255);
            $table->string('direccion', 255);
            $table->string('tipo_entrada', 255);
            $table->string('numero', 255);
            $table->string('tarea', 255);
            $table->string('latitud', 255);
            $table->string('longitud', 255);
            $table->string('observaciones', 255);
            $table->string('prioridad', 255);
            $table->string('direccion_manual', 255);
            $table->string('comentarios', 255);
            $table->string('ncaso', 255);
            $table->string('fecha_asig', 255);
            $table->string('fecha_cierre', 255);
            $table->string('contratista', 255);
            $table->string('status', 255);
            $table->string('fecha_envio', 255);
            $table->string('ult_repa', 255);
            $table->string('dias_saig', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atcreportesgpon');
    }
};
