<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('new_lista_materiales', function (Blueprint $table) {
            $table->increments('id');
            $table->string('token', 255);
            $table->integer('sap');
            $table->string('material', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('new_lista_materiales');
    }
};
