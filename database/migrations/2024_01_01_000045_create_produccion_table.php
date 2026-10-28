<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produccion', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tecnico', 255)->nullable();
            $table->date('fecha')->nullable();
            $table->string('dia', 255)->nullable();
            $table->time('horadep')->nullable();
            $table->time('horatarea')->nullable();
            $table->integer('dosplay')->nullable();
            $table->integer('tresplay')->nullable();
            $table->integer('stb')->nullable();
            $table->integer('mudanza')->nullable();
            $table->integer('tcumplida')->nullable();
            $table->integer('tareasmtto')->nullable();
            $table->integer('bajas')->nullable();
            $table->integer('bajatec')->nullable();
            $table->string('garantec', 255)->nullable();
            $table->string('garancom', 255)->nullable();
            $table->string('reclamo', 255)->nullable();
            $table->time('fin')->nullable();
            $table->string('zona', 255)->nullable();
            $table->text('obs')->nullable();
            $table->integer('mtto_int');
            $table->integer('mtto_ext');
            $table->integer('baja_desmonte');
            $table->integer('mtto_reaco');

            $table->index('tecnico');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produccion');
    }
};
