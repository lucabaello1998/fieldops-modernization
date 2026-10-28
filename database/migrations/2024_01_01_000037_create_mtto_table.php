<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla legacy ancha (32 columnas, ~29620 bytes de fila estimados):
        // excede el limite de fila de InnoDB (8126). El servidor de demo corre con
        // innodb_strict_mode=OFF (ver docker-compose.yml) para replicar el esquema original.
        Schema::create('mtto', function (Blueprint $table) {
            $table->increments('id');
            $table->string('token', 255);
            $table->string('quien', 255);
            $table->timestamp('cuando');
            $table->string('tecnico', 255);
            $table->string('ot', 255);
            $table->string('id_actividad', 255);
            $table->string('nim', 255);
            $table->string('direccion', 255);
            $table->string('localidad', 255);
            $table->string('zona_tarea', 255);
            $table->string('cliente', 255);
            $table->string('telefono', 255);
            $table->string('zona', 255);
            $table->date('fecha');
            $table->string('motivo', 255);
            $table->string('obs', 255);
            $table->string('ont_mac', 255);
            $table->string('ont_sn', 255);
            $table->string('stb_mac_uno', 255);
            $table->string('stb_sn_uno', 255);
            $table->string('stb_mac_dos', 255);
            $table->string('stb_sn_dos', 255);
            $table->string('stb_mac_tres', 255);
            $table->string('stb_sn_tres', 255);
            $table->string('ap_uno_mac', 255);
            $table->string('ap_uno_sn', 255);
            $table->string('ap_dos_mac', 255);
            $table->string('ap_dos_sn', 255);
            $table->string('ap_tres_mac', 255);
            $table->string('ap_tres_sn', 255);
            $table->string('completo', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mtto');
    }
};
