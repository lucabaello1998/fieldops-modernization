<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Datos SINTETICOS para la demo. Nunca se cargan datos productivos.
 * Se fija la semilla para que el tablero sea reproducible.
 *
 * Es idempotente a proposito: en Fly.io el contenedor arranca con RUN_SEED=1 y las
 * maquinas se apagan y vuelven a arrancar. Sin este corte, cada arranque duplicaria
 * la demo entera (los seeders de volumen usan `insert`).
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Los usuarios de demo se crean con updateOrCreate: correrlo siempre es seguro
        // y garantiza que el login de la demo exista aunque el resto ya este sembrado.
        $this->call(UserSeeder::class);

        if (DB::table('tareas')->exists()) {
            $this->command?->warn('La base ya tiene datos de demo: no se vuelve a sembrar.');

            return;
        }

        mt_srand(20261023);

        $this->call([
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
