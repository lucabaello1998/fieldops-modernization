<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('numeroSap', function (Blueprint $table) {
            $table->integer('Material Sinergia')->nullable();
            $table->string('Descripción', 40)->nullable();
            $table->string('C', 10)->nullable();
            $table->string('D', 10)->nullable();
            $table->string('E', 10)->nullable();
            $table->string('F', 18)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('numeroSap');
    }
};
