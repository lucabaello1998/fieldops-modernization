<?php

namespace Database\Seeders;

use App\Models\Produccion;
use App\Models\Tecnico;
use App\Support\DemoCatalog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Produccion diaria por tecnico: es la tabla que alimenta los graficos
 * de productividad (dos play, tres play, STB, mudanzas, mantenimiento, bajas).
 */
class ProduccionSeeder extends Seeder
{
    private const DIAS = ['Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'];

    public function run(): void
    {
        $tecnicos = Tecnico::query()->where('activo', 'Si')->pluck('tecnico')->all();

        if ($tecnicos === []) {
            return;
        }

        $filas = [];

        // Ultimos 28 dias: todo el plantel con actividad, para que el desempeno
        // por tecnico del listado y los KPIs de 30 dias tengan datos reales.
        foreach ($tecnicos as $tecnico) {
            for ($n = 0, $dias = random_int(1, 3); $n < $dias; $n++) {
                $fecha = now()->subDays(random_int(0, 27));

                if ($fecha->dayOfWeek === Carbon::SUNDAY) {
                    continue;
                }

                $filas[] = $this->fila($tecnico, $fecha);
            }
        }

        // Historico: 13 meses hacia atras, con volumen parecido al mes en curso
        // para que la curva de produccion no tenga un salto artificial al final.
        for ($mes = 13; $mes >= 1; $mes--) {
            $base = now()->subMonths($mes);
            $cantidad = random_int(300, 400);

            for ($n = 0; $n < $cantidad; $n++) {
                $fecha = $base->copy()->subDays(random_int(0, 27));

                if ($fecha->dayOfWeek === Carbon::SUNDAY) {
                    continue;
                }

                $filas[] = $this->fila(DemoCatalog::rand($tecnicos), $fecha);
            }
        }

        foreach (array_chunk($filas, 400) as $chunk) {
            Produccion::insert($chunk);
        }
    }

    /** Una jornada de produccion para un tecnico en una fecha. */
    private function fila(string $tecnico, Carbon $fecha): array
    {
        $dosplay = random_int(0, 4);
        $tresplay = random_int(0, 2);

        return [
            'tecnico' => $tecnico,
            'fecha' => $fecha->toDateString(),
            'dia' => DemoCatalog::rand(self::DIAS),
            'horadep' => sprintf('%02d:%02d:00', random_int(7, 9), random_int(0, 59)),
            'horatarea' => sprintf('%02d:%02d:00', random_int(17, 20), random_int(0, 59)),
            'dosplay' => $dosplay,
            'tresplay' => $tresplay,
            'stb' => $dosplay + $tresplay,
            'mudanza' => random_int(0, 1),
            'tcumplida' => $dosplay + $tresplay + random_int(0, 1),
            'tareasmtto' => random_int(0, 3),
            'bajas' => random_int(0, 2),
            'bajatec' => random_int(0, 1),
            'garantec' => (string) random_int(0, 1),
            'garancom' => (string) random_int(0, 1),
            'reclamo' => (string) random_int(0, 2),
            'fin' => sprintf('%02d:%02d:00', random_int(17, 21), random_int(0, 59)),
            'zona' => DemoCatalog::rand(DemoCatalog::ZONAS),
            'obs' => DemoCatalog::rand(['', '', 'Sin novedad', 'Se corto por lluvia', '']),
            'mtto_int' => random_int(0, 2),
            'mtto_ext' => random_int(0, 1),
            'baja_desmonte' => random_int(0, 1),
            'mtto_reaco' => random_int(0, 1),
        ];
    }
}
