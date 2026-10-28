<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla legacy ancha (12 columnas, ~8233 bytes de fila estimados):
        // excede el limite de fila de InnoDB (8126). El servidor de demo corre con
        // innodb_strict_mode=OFF (ver docker-compose.yml) para replicar el esquema original.
        Schema::create('mensajes_tec', function (Blueprint $table) {
            $table->increments('id');
            $table->string('token', 255);
            $table->string('token_sesion', 255);
            $table->string('quien', 255);
            $table->timestamp('cuando');
            $table->string('tecnico', 255);
            $table->string('deposito', 255);
            $table->text('consulta');
            $table->string('tipo_us', 255);
            $table->string('estado', 255);
            $table->string('tomado', 255);
            $table->string('visto', 11);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mensajes_tec');
    }
};
