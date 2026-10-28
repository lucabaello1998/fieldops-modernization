<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla legacy ancha (13 columnas, ~10221 bytes de fila estimados):
        // excede el limite de fila de InnoDB (8126). El servidor de demo corre con
        // innodb_strict_mode=OFF (ver docker-compose.yml) para replicar el esquema original.
        Schema::create('cambiotecnologia', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tecnico', 255);
            $table->integer('ot');
            $table->string('direccion', 255);
            $table->date('calendario');
            $table->string('sn_ont', 255);
            $table->string('mac_ont', 255);
            $table->string('sn_uno_stb', 255);
            $table->string('mac_uno_stb', 255);
            $table->string('sn_dos_stb', 255);
            $table->string('mac_dos_stb', 255);
            $table->string('sn_tres_stb', 255);
            $table->string('mac_tres_stb', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cambiotecnologia');
    }
};
