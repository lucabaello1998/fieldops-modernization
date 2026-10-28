<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla legacy ancha (25 columnas, ~20451 bytes de fila estimados):
        // excede el limite de fila de InnoDB (8126). El servidor de demo corre con
        // innodb_strict_mode=OFF (ver docker-compose.yml) para replicar el esquema original.
        Schema::create('auditoria_instalaciones', function (Blueprint $table) {
            $table->increments('id');
            $table->string('identificador', 255);
            $table->string('supervisor', 255);
            $table->timestamp('fecha_relevo');
            $table->string('tecnico', 255);
            $table->date('fecha');
            $table->bigInteger('ot');
            $table->string('instalacion_externa', 255);
            $table->string('foto_nomenclador', 255);
            $table->string('cadena', 255);
            $table->string('altura_acometida', 255);
            $table->string('punto_retencion', 255);
            $table->string('curva_goteo', 255);
            $table->string('ingreso_domicilio', 255);
            $table->string('engrampado_interior', 255);
            $table->string('ont', 255);
            $table->string('residuos', 255);
            $table->string('trato_cliente', 255);
            $table->string('uso_herramientas', 255);
            $table->string('epp', 255);
            $table->string('imagenpri', 255);
            $table->string('imagenseg', 255);
            $table->string('imagenter', 255);
            $table->string('imagencuar', 255);
            $table->text('obs');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditoria_instalaciones');
    }
};
