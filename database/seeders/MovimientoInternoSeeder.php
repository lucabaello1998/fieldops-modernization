<?php

namespace Database\Seeders;

use App\Models\MovimientoInterno;
use App\Support\DemoCatalog;
use Illuminate\Database\Seeder;

class MovimientoInternoSeeder extends Seeder
{
    public function run(): void
    {
        $filas = [];

        for ($n = 0; $n < 420; $n++) {
            $fecha = DemoCatalog::fechaEntre('-8 months', 'now');

            $filas[] = [
                'token' => bin2hex(random_bytes(6)),
                'quien' => DemoCatalog::rand(['despacho', 'deposito']),
                'cuando' => $fecha,
                'movimiento' => DemoCatalog::rand(['Alta de material', 'Baja de material', 'Transferencia', 'Devolucion', 'Ajuste de inventario']),
                'pag' => (string) random_int(1, 6),
                'inicio' => $fecha->copy()->addDays(random_int(1, 30))->toDateString(),
                'dia' => $fecha->locale('es')->isoFormat('dddd'),
                'tipo' => DemoCatalog::rand(['Interno', 'Externo']),
                'zona' => DemoCatalog::rand(DemoCatalog::DEPOSITOS),
                'obs' => DemoCatalog::rand(['', 'Pendiente confirmacion del deposito', 'Conforme', '']),
                'latitud' => (string) (random_int(-3465000, -3455000) / 100000),
                'longitud' => (string) (random_int(-5855000, -5843000) / 100000),
            ];
        }

        foreach (array_chunk($filas, 200) as $chunk) {
            MovimientoInterno::insert($chunk);
        }
    }
}
