<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('basemateriales', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('sap');
            $table->string('material', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('basemateriales');
    }
};
