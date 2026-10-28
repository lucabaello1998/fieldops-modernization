<?php

namespace Database\Seeders;

use App\Models\Precio;
use Illuminate\Database\Seeder;

class PrecioSeeder extends Seeder
{
    public function run(): void
    {
        // Tabla de precios unitarios por tipo de tarea (una sola fila, como el legacy).
        Precio::insert([[
            'dosplay' => 8500, 'tresplay' => 9800, 'stb' => 2200, 'mudainter' => 5200,
            'bajas' => 3400, 'garanjus' => 4100, 'garaninterv' => 2900, 'garancomp' => 3300,
            'sab' => 11000, 'fer' => 13500, 'sabmtto' => 9500, 'fermtto' => 12000,
            'bajatec' => 3100, 'bajadesmont' => 4700, 'mttointer' => 5600, 'mttoexter' => 7200,
            'desplani' => 1800, 'desepp' => 1500, 'desinst' => 2600, 'descalidad' => 2100,
            'destoa' => 1200, 'destotal' => 3400, 'desbaja' => 1400, 'mttoreacond' => 3900,
        ]]);
    }
}
