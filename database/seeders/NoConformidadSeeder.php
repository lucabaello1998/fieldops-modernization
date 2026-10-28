<?php

namespace Database\Seeders;

use App\Models\Tecnico;
use App\Models\NoConformidad;
use App\Support\DemoCatalog;
use Illuminate\Database\Seeder;

class NoConformidadSeeder extends Seeder
{
    public function run(): void
    {
        $tecnicos = Tecnico::query()->where('activo', 'Si')->pluck('tecnico')->all();
        $filas = [];

        for ($n = 0; $n < 42; $n++) {
            $fecha = DemoCatalog::fechaEntre('-10 months', 'now');
            $filas[] = [
                'quien' => DemoCatalog::rand(['Marta Sosa', 'Emiliano Paz', 'Roberto Gimenez']),
                'cuando' => $fecha,
                'tecnico' => DemoCatalog::rand($tecnicos),
                'fecha' => $fecha->toDateString(),
                'supervisor' => DemoCatalog::rand(['Marta Sosa', 'Emiliano Paz']),
                'problema' => DemoCatalog::rand([
                    'Instalacion sin roseta optica fijada.',
                    'Cable drop sin grampas en fachada.',
                    'No se dejo constancia de potencia optica.',
                    'Se retiro equipo sin registrar en el sistema.',
                    'Falta de EPP en tarea en altura.',
                ]),
                'id_nc' => 'NC-' . str_pad((string) ($n + 1), 5, '0', STR_PAD_LEFT),
                'imagenpri' => '', 'imagenseg' => '', 'imagenter' => '',
            ];
        }

        NoConformidad::insert($filas);
    }
}
