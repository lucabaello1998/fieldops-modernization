<?php

namespace Database\Seeders;

use App\Models\AsignacionMaterial;
use App\Models\Tecnico;
use App\Support\DemoCatalog;
use Illuminate\Database\Seeder;

class AsignacionMaterialSeeder extends Seeder
{
    public function run(): void
    {
        $tecnicos = Tecnico::query()->select('tecnico', 'id')->where('activo', 'Si')->get();
        $filas = [];

        for ($n = 0; $n < 900; $n++) {
            $tecnico = $tecnicos->random();
            $fecha = DemoCatalog::fechaEntre('-6 months', 'now');
            $codigo = array_rand(DemoCatalog::MATERIALES);
            $cantidad = random_int(1, 8);
            $usado = random_int(0, $cantidad);

            $filas[] = [
                'quien' => 'deposito',
                'cuando' => $fecha->copy()->addHours(random_int(8, 17)),
                'token' => bin2hex(random_bytes(6)),
                'fecha' => $fecha->toDateString(),
                'tecnico' => $tecnico->tecnico,
                'id_tec' => (string) $tecnico->id,
                'sap' => (int) $codigo,
                'material' => DemoCatalog::MATERIALES[$codigo],
                'seriado' => random_int(0, 100) <= 30 ? 'S' : 'N',
                'cantidad' => $cantidad,
                'usado' => (string) $usado,
                'ot' => random_int(100000, 999999),
                'deposito' => DemoCatalog::rand(DemoCatalog::DEPOSITOS),
                'tipo' => DemoCatalog::rand(['ENTREGA', 'ENTREGA', 'DEVOLUCION']),
                'estado' => random_int(0, 100) <= 75 ? 'Cerrada' : 'Abierta',
            ];
        }

        foreach (array_chunk($filas, 300) as $chunk) {
            AsignacionMaterial::insert($chunk);
        }
    }
}
