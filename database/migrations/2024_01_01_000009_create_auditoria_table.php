<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla legacy ancha (58 columnas, ~54237 bytes de fila estimados):
        // excede el limite de fila de InnoDB (8126). El servidor de demo corre con
        // innodb_strict_mode=OFF (ver docker-compose.yml) para replicar el esquema original.
        Schema::create('auditoria', function (Blueprint $table) {
            $table->increments('id');
            $table->string('identificador', 255);
            $table->string('supervisor', 255);
            $table->timestamp('fecha_relevo');
            $table->string('tecnico', 255);
            $table->date('fecha');
            $table->string('aire', 255);
            $table->string('alargue', 255);
            $table->string('alcohol', 255);
            $table->string('alicate', 255);
            $table->string('arnes', 255);
            $table->string('campera', 255);
            $table->string('casco', 255);
            $table->string('celular', 255);
            $table->string('chomba', 255);
            $table->string('pasacable', 255);
            $table->string('cleaver', 255);
            $table->string('conos', 255);
            $table->string('crimpeadora', 255);
            $table->string('dest_phillips', 255);
            $table->string('dest_plano', 255);
            $table->string('tension', 255);
            $table->string('enduido', 255);
            $table->string('escalera_chica', 255);
            $table->string('escalera_grande', 255);
            $table->string('escoba', 255);
            $table->string('fibron', 255);
            $table->string('gafas', 255);
            $table->string('gorra', 255);
            $table->string('alta_tension', 255);
            $table->string('guante_trabajo', 25);
            $table->string('lapiz_limpiador', 255);
            $table->string('lapiz_optico', 255);
            $table->string('linga', 255);
            $table->string('martillo', 255);
            $table->string('mecha6', 255);
            $table->string('mecha_pasante', 255);
            $table->string('pala', 255);
            $table->string('pantalon', 255);
            $table->string('panos', 255);
            $table->string('peladora_fo', 255);
            $table->string('peladora_uni', 255);
            $table->string('percutora', 255);
            $table->string('pinza', 255);
            $table->string('silicona', 255);
            $table->string('power', 255);
            $table->string('tel', 255);
            $table->string('tester_rj', 255);
            $table->string('tijera', 255);
            $table->string('zapatos', 255);
            $table->string('bolso_kit', 255);
            $table->string('bolso_cleaver', 255);
            $table->string('caja', 255);
            $table->text('obs');
            $table->string('imagenpri', 255);
            $table->string('imagenseg', 255);
            $table->string('imagenter', 255);
            $table->string('imagencuar', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditoria');
    }
};
