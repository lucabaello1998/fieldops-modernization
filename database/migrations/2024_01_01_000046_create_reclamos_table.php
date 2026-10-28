<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla legacy ancha (18 columnas, ~13220 bytes de fila estimados):
        // excede el limite de fila de InnoDB (8126). El servidor de demo corre con
        // innodb_strict_mode=OFF (ver docker-compose.yml) para replicar el esquema original.
        Schema::create('reclamos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tecnico', 255);
            $table->string('rf', 255);
            $table->integer('ot');
            $table->date('fechains');
            $table->date('fechamail');
            $table->string('direccion', 255);
            $table->bigInteger('telefono');
            $table->string('problema', 500);
            $table->date('fechasolu');
            $table->string('solucion', 500);
            $table->integer('gasto');
            $table->string('foto_uno', 255);
            $table->string('foto_dos', 255);
            $table->string('foto_tres', 255);
            $table->string('foto_cuatro', 255);
            $table->string('foto_cinco', 255);
            $table->string('foto_seis', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reclamos');
    }
};
