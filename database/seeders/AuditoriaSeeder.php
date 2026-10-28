<?php

namespace Database\Seeders;

use App\Models\Auditoria;
use App\Models\Tecnico;
use App\Support\DemoCatalog;
use Illuminate\Database\Seeder;

/**
 * Auditorias de herramientas y EPP (58 columnas en el esquema real).
 * Aca se completa el checklist con Cumple / No cumple / N/A.
 */
class AuditoriaSeeder extends Seeder
{
    private const CHECKLIST = [
        'aire', 'alargue', 'alcohol', 'alicate', 'arnes', 'campera', 'casco', 'celular',
        'chomba', 'pasacable', 'cleaver', 'conos', 'crimpeadora', 'dest_phillips',
        'dest_plano', 'tension', 'enduido', 'escalera_chica', 'escalera_grande', 'escoba',
        'fibron', 'gafas', 'gorra', 'alta_tension', 'guante_trabajo', 'lapiz_limpiador',
        'lapiz_optico', 'linga', 'martillo', 'mecha6', 'mecha_pasante', 'pala', 'pantalon',
        'panos', 'peladora_fo', 'peladora_uni', 'percutora', 'pinza', 'silicona', 'power',
        'tel', 'tester_rj', 'tijera', 'zapatos', 'bolso_kit', 'bolso_cleaver', 'caja',
    ];

    public function run(): void
    {
        $tecnicos = Tecnico::query()->where('activo', 'Si')->pluck('tecnico')->all();
        $filas = [];

        for ($n = 0; $n < 130; $n++) {
            $fecha = DemoCatalog::fechaEntre('-14 months', 'now');
            $tecnico = DemoCatalog::rand($tecnicos);
            $fila = [
                'identificador' => 'AUD-' . str_pad((string) ($n + 1), 5, '0', STR_PAD_LEFT),
                'supervisor' => DemoCatalog::rand(['Marta Sosa', 'Emiliano Paz', 'Nestor Villalba', 'Roberto Gimenez']),
                'fecha_relevo' => $fecha->copy()->addHours(random_int(9, 17)),
                'tecnico' => $tecnico,
                'fecha' => $fecha->toDateString(),
                'obs' => DemoCatalog::rand(['', '', 'Falta reponer linga.', 'Casco con vencimiento proximo.', '']),
                'imagenpri' => '', 'imagenseg' => '', 'imagenter' => '', 'imagencuar' => '',
            ];
            foreach (self::CHECKLIST as $item) {
                $fila[$item] = random_int(1, 100) <= 82 ? 'Cumple' : DemoCatalog::rand(['No cumple', 'N/A', 'Cumple']);
            }
            $filas[] = $fila;
        }

        foreach (array_chunk($filas, 50) as $chunk) {
            Auditoria::insert($chunk);
        }
    }
}
