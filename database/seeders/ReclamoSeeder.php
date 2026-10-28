<?php

namespace Database\Seeders;

use App\Models\Reclamo;
use App\Models\Tecnico;
use App\Support\DemoCatalog;
use Illuminate\Database\Seeder;

class ReclamoSeeder extends Seeder
{
    public function run(): void
    {
        $tecnicos = Tecnico::query()->where('activo', 'Si')->pluck('tecnico')->all();
        $filas = [];

        for ($n = 0; $n < 95; $n++) {
            $fecha = DemoCatalog::fechaEntre('-12 months', 'now');
            $resuelto = random_int(0, 100) <= 82;

            $filas[] = [
                'tecnico' => DemoCatalog::rand($tecnicos),
                'rf' => 'RF' . random_int(10000, 99999),
                'ot' => random_int(100000, 999999),
                'fechains' => $fecha->toDateString(),
                'fechamail' => $fecha->copy()->addDays(random_int(0, 3))->toDateString(),
                'direccion' => DemoCatalog::rand(['Av. Rivadavia', 'Calle Mitre', 'Av. San Martin', 'Calle Belgrano']) . ' ' . random_int(100, 4500),
                'telefono' => random_int(1140000000, 1179999999),
                'problema' => DemoCatalog::rand([
                    'Intermitencia en el servicio de internet',
                    'Baja velocidad en horario pico',
                    'Sin senal de TV',
                    'Corte total del servicio',
                    'Ruido en la linea telefonica',
                    'WiFi con baja cobertura',
                ]),
                // En el esquema real `fechasolu` y `solucion` son NOT NULL: los reclamos
                // abiertos quedan con la fecha de ingreso y la solucion vacia.
                'fechasolu' => $resuelto
                    ? $fecha->copy()->addDays(random_int(1, 7))->toDateString()
                    : $fecha->toDateString(),
                'solucion' => $resuelto ? DemoCatalog::rand([
                    'Se reemplazo conector SC/APC y se midio potencia.',
                    'Se cambio el splitter del gabinete.',
                    'Se reconfiguro el decodificador.',
                    'Se reemplazo el cable drop por dano mecanico.',
                ]) : '',
                'gasto' => $resuelto ? random_int(0, 45000) : 0,
                'foto_uno' => '', 'foto_dos' => '', 'foto_tres' => '',
                'foto_cuatro' => '', 'foto_cinco' => '', 'foto_seis' => '',
            ];
        }

        foreach (array_chunk($filas, 50) as $chunk) {
            Reclamo::insert($chunk);
        }
    }
}
