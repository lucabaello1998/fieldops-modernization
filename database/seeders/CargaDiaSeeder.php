<?php

namespace Database\Seeders;

use App\Models\CargaDia;
use App\Models\Tecnico;
use App\Support\DemoCatalog;
use Illuminate\Database\Seeder;

/**
 * Carga diaria: el despacho de ordenes del dia (la pantalla que el legacy
 * servia con carga_dia.php). Alimenta "actividad del dia" en el tablero.
 */
class CargaDiaSeeder extends Seeder
{
    public function run(): void
    {
        $tecnicos = Tecnico::query()->where('activo', 'Si')->pluck('tecnico')->all();
        $filas = [];

        for ($n = 0; $n < 1700; $n++) {
            $fecha = DemoCatalog::fechaEntre('-95 days', 'now');
            $tecnico = DemoCatalog::rand($tecnicos);
            $actividad = DemoCatalog::rand(DemoCatalog::ACTIVIDADES);
            $completada = random_int(0, 100) <= 78;
            $inicio = random_int(7, 15);

            $filas[] = [
                'quien' => 'despacho',
                'cuando' => $fecha->copy()->setTime($inicio, random_int(0, 59)),
                'tecnico' => $tecnico,
                'id_actividad' => (string) random_int(1000000, 9999999),
                'ot' => (string) random_int(100000, 999999),
                'direccion' => DemoCatalog::rand(['Av. Rivadavia', 'Calle Mitre', 'Av. San Martin', 'Calle Belgrano', 'Av. Mitre']) . ' ' . random_int(100, 4500),
                'localidad' => DemoCatalog::rand(DemoCatalog::LOCALIDADES),
                'zona' => DemoCatalog::rand(DemoCatalog::ZONAS),
                'fecha' => $fecha->toDateString(),
                'intervalo' => DemoCatalog::rand(['08:00-12:00', '09:00-13:00', '12:00-16:00', '14:00-18:00']),
                'actividad' => $actividad,
                'codigo' => (string) random_int(100, 999),
                'cantidad_tv' => (string) random_int(1, 3),
                'estado' => $completada ? 'Completada' : DemoCatalog::rand(['En curso', 'No completada', 'Pendiente']),
                'razon_completada' => $completada ? 'Cliente satisfecho' : '',
                'razon_no_completada' => $completada ? '' : DemoCatalog::rand(['Cliente ausente', 'Falta de material', 'Direccion incorrecta', 'Sin acceso al predio']),
                'nota_cierre' => DemoCatalog::rand(['', '', 'Se dejo conforme', 'Pendiente firma']),
                'inicio' => sprintf('%02d:%02d:00', $inicio, random_int(0, 59)),
                'fin' => sprintf('%02d:%02d:00', min(23, $inicio + random_int(1, 4)), random_int(0, 59)),
                'duracion' => (string) random_int(45, 240),
                'cliente' => DemoCatalog::rand(['Maria Gonzalez', 'Carlos Dominguez', 'Lucia Fernandez', 'Jorge Benitez', 'Silvia Rios', 'Consorcio Mitre 500']),
                'telefono' => '11' . random_int(40000000, 79999999),
                'nim' => 'NIM' . strtoupper(bin2hex(random_bytes(3))),
                'motivo_asignacion' => DemoCatalog::rand(['Zona mas cercana', 'Reasignado', 'Pedido del cliente', 'Balanceo de carga']),
                'revisita' => random_int(0, 100) <= 12 ? 'Si' : 'No',
                'obs' => '',
                'dos_play' => random_int(0, 1),
                'tres_play' => random_int(0, 1),
                'stb' => random_int(0, 2),
                'mudanza_interna' => random_int(0, 1),
                'baja' => random_int(0, 1),
                'garantia' => random_int(0, 1),
                'garantia_justificada' => random_int(0, 1),
                'garantia_com' => random_int(0, 1),
                'baja_tecnica' => random_int(0, 1),
                'baja_desmonte' => random_int(0, 1),
                'mtto' => random_int(0, 2),
                'mtto_externo' => random_int(0, 1),
                'reacondicionamiento' => random_int(0, 1),
                'dia' => $fecha->locale('es')->isoFormat('dddd'),
                'zona_recurso' => DemoCatalog::rand(DemoCatalog::ZONAS),
                'ingresado' => 'Si',
                'verificado' => $completada ? 'Si' : 'No',
            ];
        }

        foreach (array_chunk($filas, 400) as $chunk) {
            CargaDia::insert($chunk);
        }
    }
}
