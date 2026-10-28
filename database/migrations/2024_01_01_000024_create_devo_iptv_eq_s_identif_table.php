<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devo_iptv_eq_s_identif', function (Blueprint $table) {
            $table->integer('id')->nullable();
            $table->string('Tipo de devolución', 45)->nullable();
            $table->integer('N° REMITO')->nullable();
            $table->string('Status', 10)->nullable();
            $table->string('CONTRATISTA', 13)->nullable();
            $table->string('REFERENTE', 11)->nullable();
            $table->string('CENTRO', 4)->nullable();
            $table->string('ALM CONTRATISTA', 4)->nullable();
            $table->string('CENTRO DESTINO', 10)->nullable();
            $table->string('ALMACEN DESTINO', 10)->nullable();
            $table->string('OPERATORIA', 10)->nullable();
            $table->string('N° PALLET', 10)->nullable();
            $table->integer('MATERIAL SAP')->nullable();
            $table->string('S/N', 17)->nullable();
            $table->integer('CANTIDAD')->nullable();
            $table->integer('Nº CAJA')->nullable();
            $table->string('DESCRIPCION', 40)->nullable();
            $table->string('SITIO', 17)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devo_iptv_eq_s_identif');
    }
};
