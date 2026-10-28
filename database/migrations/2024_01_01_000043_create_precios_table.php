<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('precios', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('dosplay');
            $table->integer('tresplay');
            $table->integer('stb');
            $table->integer('mudainter');
            $table->integer('bajas');
            $table->integer('garanjus');
            $table->integer('garaninterv');
            $table->integer('garancomp');
            $table->integer('sab');
            $table->integer('fer');
            $table->integer('sabmtto');
            $table->integer('fermtto');
            $table->integer('bajatec');
            $table->integer('bajadesmont');
            $table->integer('mttointer');
            $table->integer('mttoexter');
            $table->integer('desplani');
            $table->integer('desepp');
            $table->integer('desinst');
            $table->integer('descalidad');
            $table->integer('destoa');
            $table->integer('destotal');
            $table->integer('desbaja');
            $table->integer('mttoreacond');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('precios');
    }
};
