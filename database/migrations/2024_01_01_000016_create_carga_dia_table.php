<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla legacy ancha (44 columnas, ~26615 bytes de fila estimados):
        // excede el limite de fila de InnoDB (8126). El servidor de demo corre con
        // innodb_strict_mode=OFF (ver docker-compose.yml) para replicar el esquema original.
        Schema::create('carga_dia', function (Blueprint $table) {
            $table->increments('id');
            $table->string('quien', 255);
            $table->timestamp('cuando');
            $table->string('tecnico', 255);
            $table->string('id_actividad', 255);
            $table->string('ot', 255);
            $table->string('direccion', 255);
            $table->string('localidad', 255);
            $table->string('zona', 255);
            $table->date('fecha');
            $table->string('intervalo', 255);
            $table->string('actividad', 255);
            $table->string('codigo', 255);
            $table->string('cantidad_tv', 255);
            $table->string('estado', 255);
            $table->string('razon_completada', 255);
            $table->string('razon_no_completada', 255);
            $table->string('nota_cierre', 255);
            $table->time('inicio');
            $table->time('fin');
            $table->string('duracion', 255);
            $table->string('cliente', 255);
            $table->string('telefono', 255);
            $table->string('nim', 255);
            $table->string('motivo_asignacion', 255);
            $table->string('revisita', 255);
            $table->string('obs', 255);
            $table->integer('dos_play');
            $table->integer('tres_play');
            $table->integer('stb');
            $table->integer('mudanza_interna');
            $table->integer('baja');
            $table->integer('garantia');
            $table->integer('garantia_justificada');
            $table->integer('garantia_com');
            $table->integer('baja_tecnica');
            $table->integer('baja_desmonte');
            $table->integer('mtto');
            $table->integer('mtto_externo');
            $table->integer('reacondicionamiento');
            $table->string('dia', 255);
            $table->string('zona_recurso', 255);
            $table->string('ingresado', 255);
            $table->string('verificado', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carga_dia');
    }
};
