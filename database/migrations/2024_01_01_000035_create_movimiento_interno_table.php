<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla legacy ancha (13 columnas, ~9212 bytes de fila estimados):
        // excede el limite de fila de InnoDB (8126). El servidor de demo corre con
        // innodb_strict_mode=OFF (ver docker-compose.yml) para replicar el esquema original.
        Schema::create('movimiento_interno', function (Blueprint $table) {
            $table->increments('id');
            $table->string('token', 255);
            $table->string('quien', 255);
            $table->timestamp('cuando');
            $table->string('movimiento', 255);
            $table->string('pag', 255);
            $table->date('inicio');
            $table->string('dia', 255);
            $table->string('tipo', 255);
            $table->string('zona', 255);
            $table->text('obs');
            $table->string('latitud', 255);
            $table->string('longitud', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimiento_interno');
    }
};
