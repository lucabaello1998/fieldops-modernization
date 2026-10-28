<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('indumentaria', function (Blueprint $table) {
            $table->increments('id');
            $table->string('indumentaria', 255);
            $table->string('talle', 255);
            $table->integer('cantidad');
            $table->string('sap', 255);
            $table->string('pedido', 255);
            $table->date('fecha');
            $table->date('fechaegre');
            $table->string('tecnico', 255);
            $table->integer('cantec');
            $table->string('centro', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('indumentaria');
    }
};
