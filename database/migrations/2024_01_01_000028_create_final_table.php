<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Tabla legacy `final`: tiene columnas con nombres que el generador de esquema de
 * Laravel no puede escapar (por ejemplo `CANT. MAXIMA`, donde el punto se
 * interpretaria como separador tabla.columna). Se crea con SQL crudo tal cual el original.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement(<<<'SQL'
CREATE TABLE `final` (
  `&2` varchar(25),
  `Fecha Análisis` varchar(10),
  `CLAVE` varchar(25),
  `Material` varchar(10),
  `Texto breve de material` varchar(133),
  `AUTORIZADO INFRA` varchar(19),
  `ORIGEN MATERIAL` varchar(14),
  `SERIADO` varchar(15),
  `UB DESTINO` int,
  `CLASIFICACION` varchar(4),
  `reserva CAR (SI)` varchar(1),
  `reserva CAR (NO)` varchar(3),
  `CANT. MAXIMA` varchar(3),
  `AUTORIZA` varchar(12),
  `REPORTE OBSOLESCENCIA OLA 1` varchar(15),
  `OBSERVACION` varchar(3),
  `Q` varchar(63),
  `R` varchar(24)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
SQL);
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS `final`');
    }
};
