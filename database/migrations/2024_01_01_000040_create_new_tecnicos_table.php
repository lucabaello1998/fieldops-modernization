<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla legacy ancha (25 columnas, ~18567 bytes de fila estimados):
        // excede el limite de fila de InnoDB (8126). El servidor de demo corre con
        // innodb_strict_mode=OFF (ver docker-compose.yml) para replicar el esquema original.
        Schema::create('new_tecnicos', function (Blueprint $table) {
            $table->increments('id');
            $table->text('token');
            $table->string('tecnico', 255);
            $table->string('nombre', 255);
            $table->string('apellido', 255);
            $table->integer('dni');
            $table->integer('id_recurso');
            $table->string('chomba', 255);
            $table->string('pantalon', 255);
            $table->string('zapato', 255);
            $table->date('ingreso');
            $table->string('modelo', 255);
            $table->string('sn', 255);
            $table->string('flota', 20);
            $table->string('tel', 20);
            $table->string('mail', 255);
            $table->string('patente', 255);
            $table->string('tusu', 255);
            $table->string('tcon', 255);
            $table->string('sgtusu', 255);
            $table->string('sgtcon', 255);
            $table->string('activo', 255);
            $table->string('zona', 255);
            $table->string('tipo', 255);
            $table->string('deposito', 255);

            $table->index('tecnico');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('new_tecnicos');
    }
};
