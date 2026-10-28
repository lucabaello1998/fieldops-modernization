<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla legacy ancha (10 columnas, ~8176 bytes de fila estimados):
        // excede el limite de fila de InnoDB (8126). El servidor de demo corre con
        // innodb_strict_mode=OFF (ver docker-compose.yml) para replicar el esquema original.
        Schema::create('atckilometros', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 255);
            $table->string('fecha', 255);
            $table->string('hora', 255);
            $table->string('fin', 255);
            $table->string('dia', 255);
            $table->string('km', 255);
            $table->integer('reportes');
            $table->string('partido', 255);
            $table->string('obs', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atckilometros');
    }
};
