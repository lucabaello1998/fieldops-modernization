<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla legacy ancha (18 columnas, ~14317 bytes de fila estimados):
        // excede el limite de fila de InnoDB (8126). El servidor de demo corre con
        // innodb_strict_mode=OFF (ver docker-compose.yml) para replicar el esquema original.
        Schema::create('cierre_tarea', function (Blueprint $table) {
            $table->increments('id');
            $table->string('token', 255);
            $table->string('quien', 255);
            $table->timestamp('cuando');
            $table->string('tecnico', 255);
            $table->string('ot', 255);
            $table->string('tkl', 255);
            $table->string('tipo_tarea', 255);
            $table->date('fecha');
            $table->string('zona', 255);
            $table->string('latitud', 255);
            $table->string('longitud', 255);
            $table->text('obs');
            $table->string('estado', 255);
            $table->string('imagenpri', 255);
            $table->string('imagenseg', 255);
            $table->string('imagenter', 255);
            $table->string('imagencuar', 255);

            $table->index('tecnico');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cierre_tarea');
    }
};
