<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla legacy ancha (31 columnas, ~37324 bytes de fila estimados):
        // excede el limite de fila de InnoDB (8126). El servidor de demo corre con
        // innodb_strict_mode=OFF (ver docker-compose.yml) para replicar el esquema original.
        Schema::create('altas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('token', 255);
            $table->string('quien', 255);
            $table->timestamp('cuando');
            $table->string('tecnico', 255);
            $table->integer('ot');
            $table->string('id_actividad', 255);
            $table->string('nim', 255);
            $table->string('direccion', 255);
            $table->string('localidad', 255);
            $table->string('zona_tarea', 255);
            $table->string('cliente', 255);
            $table->string('telefono', 255);
            $table->string('zona', 255);
            $table->date('calendario');
            $table->text('sn_ont');
            $table->text('mac_ont');
            $table->string('sn_uno_stb', 500);
            $table->string('mac_uno_stb', 500);
            $table->string('sn_dos_stb', 500);
            $table->string('mac_dos_stb', 500);
            $table->string('sn_tres_stb', 500);
            $table->string('mac_tres_stb', 500);
            $table->string('ap_uno_mac', 500);
            $table->string('ap_uno_sn', 500);
            $table->string('ap_dos_mac', 500);
            $table->string('ap_dos_sn', 500);
            $table->string('ap_tres_mac', 500);
            $table->string('ap_tres_sn', 500);
            $table->string('deposito', 255);
            $table->string('completo', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('altas');
    }
};
