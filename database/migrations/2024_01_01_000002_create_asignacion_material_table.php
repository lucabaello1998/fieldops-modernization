<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla legacy ancha (16 columnas, ~9313 bytes de fila estimados):
        // excede el limite de fila de InnoDB (8126). El servidor de demo corre con
        // innodb_strict_mode=OFF (ver docker-compose.yml) para replicar el esquema original.
        Schema::create('asignacion_material', function (Blueprint $table) {
            $table->increments('id');
            $table->string('quien', 255);
            $table->timestamp('cuando');
            $table->string('token', 255);
            $table->date('fecha');
            $table->string('tecnico', 255);
            $table->string('id_tec', 255);
            $table->integer('sap');
            $table->string('material', 255);
            $table->string('seriado', 255);
            $table->integer('cantidad');
            $table->string('usado', 255);
            $table->integer('ot');
            $table->string('deposito', 255);
            $table->string('tipo', 255);
            $table->string('estado', 25);

            $table->index('tipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asignacion_material');
    }
};
