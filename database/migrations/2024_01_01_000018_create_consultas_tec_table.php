<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultas_tec', function (Blueprint $table) {
            $table->increments('id');
            $table->string('token', 255);
            $table->string('quien', 255);
            $table->timestamp('cuando');
            $table->string('nivel', 255);
            $table->string('padre', 255);
            $table->text('texto');
            $table->string('copiable', 11);
            $table->string('tipo', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultas_tec');
    }
};
