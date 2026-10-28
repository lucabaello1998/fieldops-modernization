<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla legacy ancha (13 columnas, ~8195 bytes de fila estimados):
        // excede el limite de fila de InnoDB (8126). El servidor de demo corre con
        // innodb_strict_mode=OFF (ver docker-compose.yml) para replicar el esquema original.
        Schema::create('desarrollo', function (Blueprint $table) {
            $table->increments('id');
            $table->string('token', 255);
            $table->string('quien', 255);
            $table->timestamp('cuando');
            $table->string('titulo', 255);
            $table->date('fecha');
            $table->text('notas');
            $table->string('imagenpri', 255);
            $table->string('imagenseg', 255);
            $table->string('imagenter', 255);
            $table->string('imagencuar', 255);
            $table->integer('fase');
            $table->string('version_dev', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('desarrollo');
    }
};
