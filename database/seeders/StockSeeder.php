<?php

namespace Database\Seeders;

use App\Models\Basematerial;
use App\Models\Entregamaterial;
use App\Models\Material;
use App\Models\NumeroSap;
use App\Models\Tecnico;
use App\Support\DemoCatalog;
use Illuminate\Database\Seeder;

/**
 * Stock: catalogo base + stock por deposito + entregas a tecnicos.
 * `materiales.centro` es el deposito y `materiales.cantidad` el stock actual.
 */
class StockSeeder extends Seeder
{
    public function run(): void
    {
        $base = [];
        $sap = [];
        $i = 0;

        foreach (DemoCatalog::MATERIALES as $codigo => $descripcion) {
            $base[] = ['sap' => (int) $codigo, 'material' => $descripcion];
            $sap[] = [
                'Material Sinergia' => (int) $codigo,
                'Descripción' => mb_substr($descripcion, 0, 40),
                'C' => 'UN',
                'D' => 'EA',
                'E' => 'PZA',
                'F' => str_pad((string) random_int(1, 999), 18, '0', STR_PAD_LEFT),
            ];
            $i++;
        }

        Basematerial::insert($base);

        foreach (array_chunk($sap, 50) as $chunk) {
            NumeroSap::insert($chunk);
        }

        // Stock por deposito: no todos los depositos tienen todos los materiales.
        $stock = [];
        foreach (DemoCatalog::DEPOSITOS as $deposito) {
            foreach (DemoCatalog::MATERIALES as $codigo => $descripcion) {
                if (random_int(1, 100) > 82) {
                    continue; // el deposito no maneja ese material
                }
                // 12% de los combos en nivel critico
                $cantidad = random_int(1, 100) <= 12 ? random_int(0, 14) : random_int(15, 320);
                $fecha = DemoCatalog::fechaEntre('-400 days', 'now');

                $stock[] = [
                    'material' => $descripcion,
                    'centro' => $deposito,
                    'cantidad' => $cantidad,
                    'fecha' => $fecha->toDateString(),
                    'fechaegre' => $fecha->copy()->addDays(random_int(1, 40))->toDateString(),
                    'tecnico' => '',
                    'cantec' => 0,
                ];
            }
        }

        foreach (array_chunk($stock, 200) as $chunk) {
            Material::insert($chunk);
        }

        // Entregas de material a tecnicos (historial).
        $tecnicos = Tecnico::query()->pluck('tecnico')->all();
        $entregas = [];
        for ($n = 0; $n < 1600; $n++) {
            $codigo = array_rand(DemoCatalog::MATERIALES);
            $fecha = DemoCatalog::fechaEntre('-14 months', 'now');
            $entregas[] = [
                'tecnico' => DemoCatalog::rand($tecnicos),
                'fechaegre' => $fecha->toDateString(),
                'centro' => DemoCatalog::rand(DemoCatalog::DEPOSITOS),
                'cantidad' => random_int(1, 12),
                'material' => DemoCatalog::MATERIALES[$codigo],
            ];
        }

        foreach (array_chunk($entregas, 400) as $chunk) {
            Entregamaterial::insert($chunk);
        }
    }
}
