<?php

namespace Database\Seeders;

use App\Models\Baja;
use App\Models\Tecnico;
use App\Support\DemoCatalog;
use Illuminate\Database\Seeder;

class BajaSeeder extends Seeder
{
    public function run(): void
    {
        $tecnicos = Tecnico::query()->where('activo', 'Si')->pluck('tecnico')->all();
        $filas = [];

        for ($n = 0; $n < 285; $n++) {
            $fecha = DemoCatalog::fechaEntre('-14 months', 'now');
            $tecnico = DemoCatalog::rand($tecnicos);

            $filas[] = [
                'token' => bin2hex(random_bytes(6)),
                'quien' => $tecnico,
                'cuando' => $fecha->copy()->addHours(random_int(8, 19)),
                'tecnico' => $tecnico,
                'ot' => random_int(100000, 999999),
                'id_actividad' => (string) random_int(1000000, 9999999),
                'nim' => 'NIM'.strtoupper(bin2hex(random_bytes(3))),
                'tkl' => strtoupper(bin2hex(random_bytes(5))),
                'motivo' => DemoCatalog::rand(DemoCatalog::MOTIVOS_BAJA),
                'obs_tecnico' => DemoCatalog::rand([
                    'Se retiro ONT y decodificador.',
                    'Cliente ausente, se dejo aviso.',
                    'Equipo completo devuelto al deposito.',
                    'Solo se retiro el decodificador.',
                ]),
                'obs' => DemoCatalog::rand(['OK', 'Pendiente de cierre administrativo', 'Equipo con daño cosmetico']),
                'direccion' => DemoCatalog::rand(['Av. Rivadavia', 'Calle Mitre', 'Av. San Martin', 'Calle Belgrano']).' '.random_int(100, 4500),
                'localidad' => DemoCatalog::rand(DemoCatalog::LOCALIDADES),
                'zona_tarea' => DemoCatalog::rand(DemoCatalog::ZONAS),
                'cliente' => DemoCatalog::rand(['Maria Gonzalez', 'Carlos Dominguez', 'Lucia Fernandez', 'Jorge Benitez', 'Silvia Rios']),
                'telefono' => '11'.random_int(40000000, 79999999),
                'zona' => DemoCatalog::rand(DemoCatalog::ZONAS),
                'calendario' => $fecha->toDateString(),
            ];
        }

        foreach (array_chunk($filas, 200) as $chunk) {
            Baja::insert($chunk);
        }
    }
}
