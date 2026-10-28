<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materiales', function (Blueprint $table) {
            $table->increments('id');
            $table->string('material', 255);
            $table->string('centro', 255);
            $table->integer('cantidad');
            $table->date('fecha');
            $table->date('fechaegre');
            $table->string('tecnico', 255);
            $table->integer('cantec');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materiales');
    }
};
