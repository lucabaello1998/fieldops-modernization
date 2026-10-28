<?php

namespace Database\Seeders;

use App\Models\Alta;
use App\Models\Tecnico;
use App\Support\DemoCatalog;
use Illuminate\Database\Seeder;

/**
 * Altas (instalaciones completadas). `calendario` alimenta el grafico mensual.
 */
class AltaSeeder extends Seeder
{
    public function run(): void
    {
        $tecnicos = Tecnico::query()->where('activo', 'Si')->pluck('tecnico')->all();
        $filas = [];
        $saps = array_keys(DemoCatalog::MATERIALES);

        for ($n = 0; $n < 640; $n++) {
            $fecha = DemoCatalog::fechaEntre('-14 months', 'now');
            $tecnico = DemoCatalog::rand($tecnicos);
            $dosPlay = random_int(0, 100) <= 68;
            $tresPlay = ! $dosPlay && random_int(0, 100) <= 40;
            $stb = random_int(1, 3);

            $filas[] = [
                'token' => bin2hex(random_bytes(6)),
                'quien' => $tecnico,
                'cuando' => $fecha->copy()->addHours(random_int(8, 19)),
                'tecnico' => $tecnico,
                'ot' => random_int(100000, 999999),
                'id_actividad' => (string) random_int(1000000, 9999999),
                'nim' => 'NIM' . strtoupper(bin2hex(random_bytes(3))),
                'direccion' => DemoCatalog::rand(['Av. Rivadavia', 'Calle Mitre', 'Av. San Martin', 'Calle Belgrano', 'Av. Mitre', 'Calle Sarmiento', 'Av. Libertador'])
                    . ' ' . random_int(100, 4500),
                'localidad' => DemoCatalog::rand(DemoCatalog::LOCALIDADES),
                'zona_tarea' => DemoCatalog::rand(DemoCatalog::ZONAS),
                'cliente' => DemoCatalog::rand(['Maria Gonzalez', 'Carlos Dominguez', 'Lucia Fernandez', 'Jorge Benitez', 'Silvia Rios', 'Ricardo Sosa', 'Andrea Campero']),
                'telefono' => '11' . random_int(40000000, 79999999),
                'zona' => DemoCatalog::rand(DemoCatalog::ZONAS),
                'calendario' => $fecha->toDateString(),
                'sn_ont' => strtoupper(bin2hex(random_bytes(8))),
                'mac_ont' => strtoupper(implode(':', str_split(bin2hex(random_bytes(6)), 2))),
                'sn_uno_stb' => $stb >= 1 ? strtoupper(bin2hex(random_bytes(6))) : '',
                'mac_uno_stb' => $stb >= 1 ? strtoupper(implode(':', str_split(bin2hex(random_bytes(6)), 2))) : '',
                'sn_dos_stb' => $stb >= 2 ? strtoupper(bin2hex(random_bytes(6))) : '',
                'mac_dos_stb' => $stb >= 2 ? strtoupper(implode(':', str_split(bin2hex(random_bytes(6)), 2))) : '',
                'sn_tres_stb' => $stb >= 3 ? strtoupper(bin2hex(random_bytes(6))) : '',
                'mac_tres_stb' => $stb >= 3 ? strtoupper(implode(':', str_split(bin2hex(random_bytes(6)), 2))) : '',
                'ap_uno_mac' => strtoupper(implode(':', str_split(bin2hex(random_bytes(6)), 2))),
                'ap_uno_sn' => strtoupper(bin2hex(random_bytes(6))),
                'ap_dos_mac' => '', 'ap_dos_sn' => '',
                'ap_tres_mac' => '', 'ap_tres_sn' => '',
                'deposito' => DemoCatalog::rand(DemoCatalog::DEPOSITOS),
                'completo' => DemoCatalog::rand(['Si', 'Si', 'Si', 'No']),
            ];
        }

        foreach (array_chunk($filas, 200) as $chunk) {
            Alta::insert($chunk);
        }
    }
}
