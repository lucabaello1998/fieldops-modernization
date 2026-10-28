<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Campos que el sistema legacy `usuarios` tenia y que el modelo de auth nuevo necesita:
 * rol, estado, zona y ultimo acceso. (La password va hasheada por el cast del modelo.)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 50)->default('Tecnico')->after('password');
            $table->boolean('activo')->default(true)->after('role');
            $table->string('zona', 100)->nullable()->after('activo');
            $table->string('abreviatura', 10)->nullable()->after('zona');
            $table->timestamp('ultimo_acceso')->nullable()->after('abreviatura');
            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropColumn(['role', 'activo', 'zona', 'abreviatura', 'ultimo_acceso']);
        });
    }
};
