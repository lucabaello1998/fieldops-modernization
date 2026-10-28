<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Datos SINTETICOS para la demo. Nunca se cargan datos productivos.
 * Se fija la semilla para que el tablero sea reproducible.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        mt_srand(20261023);

        $this->call([
            UserSeeder::class,
            UsuarioLegacySeeder::class,
            TecnicoSeeder::class,
            VehiculoSeeder::class,
            PrecioSeeder::class,
            StockSeeder::class,
            TareaSeeder::class,
            AltaSeeder::class,
            BajaSeeder::class,
            ProduccionSeeder::class,
            CargaDiaSeeder::class,
            AuditoriaSeeder::class,
            ReclamoSeeder::class,
            MovimientoInternoSeeder::class,
            AsignacionMaterialSeeder::class,
            NoConformidadSeeder::class,
        ]);
    }
}
