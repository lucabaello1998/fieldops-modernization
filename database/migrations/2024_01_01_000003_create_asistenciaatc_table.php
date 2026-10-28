<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asistenciaatc', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 255);
            $table->date('fecha');
            $table->time('hora');
            $table->string('dia', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asistenciaatc');
    }
};
