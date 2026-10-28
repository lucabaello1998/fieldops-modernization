<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('corpo', function (Blueprint $table) {
            $table->increments('ID');
            $table->integer('CT')->nullable();
            $table->date('FECHA')->nullable();
            $table->string('TAREA', 25)->nullable();
            $table->string('CLIENTE', 75)->nullable();
            $table->string('ORDEN', 15)->nullable();
            $table->string('ENLACE', 15)->nullable();
            $table->string('ASIGNADO', 20)->nullable();
            $table->string('CERTIFICACION', 100)->nullable();
            $table->string('LINK_SYTEX', 150)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('corpo');
    }
};
