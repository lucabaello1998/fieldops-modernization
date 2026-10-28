<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla legacy ancha (17 columnas, ~11350 bytes de fila estimados):
        // excede el limite de fila de InnoDB (8126). El servidor de demo corre con
        // innodb_strict_mode=OFF (ver docker-compose.yml) para replicar el esquema original.
        Schema::create('ingresomaterial', function (Blueprint $table) {
            $table->increments('id');
            $table->string('usuario', 255);
            $table->timestamp('fecha_usuario');
            $table->date('fecha');
            $table->string('proveedor', 255);
            $table->string('num_pedido', 255);
            $table->string('operatoria', 255);
            $table->string('seriado', 255);
            $table->integer('sap');
            $table->string('material', 255);
            $table->string('s/n', 25);
            $table->string('cantidad', 255);
            $table->string('deposito', 255);
            $table->string('obs', 255);
            $table->string('ot', 255);
            $table->string('descargado_por', 255);
            $table->date('descargado_cuando');

            $table->index('seriado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingresomaterial');
    }
};
