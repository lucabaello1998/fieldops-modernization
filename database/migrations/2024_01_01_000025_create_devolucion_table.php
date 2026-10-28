<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla legacy ancha (26 columnas, ~15365 bytes de fila estimados):
        // excede el limite de fila de InnoDB (8126). El servidor de demo corre con
        // innodb_strict_mode=OFF (ver docker-compose.yml) para replicar el esquema original.
        Schema::create('devolucion', function (Blueprint $table) {
            $table->increments('id');
            $table->string('token', 255);
            $table->string('quien', 255);
            $table->timestamp('cuando');
            $table->date('fecha');
            $table->string('tecnico', 255);
            $table->string('ot', 255);
            $table->string('tipo', 255);
            $table->string('statuss', 255);
            $table->integer('sap');
            $table->string('material', 255);
            $table->string('sn', 255);
            $table->integer('cantidad');
            $table->string('deposito', 255);
            $table->text('obs');
            $table->date('fecha_retiro');
            $table->string('estado', 255);
            $table->integer('num_remito');
            $table->integer('legajo');
            $table->string('contratista', 255);
            $table->string('referente', 255);
            $table->string('centro', 255);
            $table->string('almacen', 255);
            $table->integer('pallet');
            $table->integer('num_caja');
            $table->string('sitio', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devolucion');
    }
};
