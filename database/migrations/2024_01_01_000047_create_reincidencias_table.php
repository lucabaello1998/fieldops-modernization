<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla legacy ancha (21 columnas, ~14336 bytes de fila estimados):
        // excede el limite de fila de InnoDB (8126). El servidor de demo corre con
        // innodb_strict_mode=OFF (ver docker-compose.yml) para replicar el esquema original.
        Schema::create('reincidencias', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tecnico', 255);
            $table->integer('ot');
            $table->string('nim', 255);
            $table->string('direccion', 255);
            $table->string('zona', 255);
            $table->date('fechaint');
            $table->date('fecharep');
            $table->string('tecrep', 255);
            $table->string('coment', 255);
            $table->string('repa', 255);
            $table->string('justificado', 255);
            $table->string('intervencion', 255);
            $table->string('responsabilidad', 255);
            $table->text('obs');
            $table->text('obs_supervisor');
            $table->integer('otdos');
            $table->string('motivo', 255);
            $table->string('abonado', 255);
            $table->string('primercierre', 255);
            $table->string('primeranota', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reincidencias');
    }
};
