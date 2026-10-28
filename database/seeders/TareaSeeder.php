<?php

namespace Database\Seeders;

use App\Models\Tarea;
use App\Models\Tecnico;
use App\Support\DemoCatalog;
use Illuminate\Database\Seeder;

/**
 * Tareas del tablero Kanban.
 * `estado` = columna del tablero; `sub_estado` = estado de la subtarea.
 */
class TareaSeeder extends Seeder
{
    private const ESTADOS = ['Pendiente', 'En curso', 'En revision', 'Finalizado'];

    private const COLORES = [
        'Pendiente' => 'slate',
        'En curso' => 'indigo',
        'En revision' => 'sky',
        'Finalizado' => 'emerald',
    ];

    private const CLIENTES = [
        'Maria Gonzalez', 'Carlos Dominguez', 'Lucia Fernandez', 'Jorge Benitez', 'Silvia Rios',
        'Consorcio Av. Rivadavia 4520', 'Hotel Solar del Plata', 'Farmacia Del Pueblo',
        'Ricardo Sosa', 'Andrea Campero', 'Esteban Pujol', 'Marta Cuello',
        'Panaderia La Espiga', 'Kiosco El Sol', 'Gimnasio Atlantis', 'Clinica San Lucas',
        'Bar Los Amigos', 'Supermercado Norte', 'Veterinaria Huellas', 'Estudio Contable PR',
    ];

    public function run(): void
    {
        $tecnicos = Tecnico::query()->where('activo', 'Si')->pluck('tecnico')->all();
        $tipos = DemoCatalog::TIPOS_TAREA;
        $filas = [];
        $total = 118;

        for ($n = 1; $n <= $total; $n++) {
            $estado = match (true) {
                $n <= 30 => 'Pendiente',
                $n <= 58 => 'En curso',
                $n <= 74 => 'En revision',
                default => 'Finalizado',
            };
            $tipo = DemoCatalog::rand($tipos);
            $ot = random_int(100000, 999999);
            $zona = DemoCatalog::rand(DemoCatalog::ZONAS);
            $cliente = DemoCatalog::rand(self::CLIENTES);
            $tecnico = DemoCatalog::rand($tecnicos);
            $creado = DemoCatalog::fechaEntre('-90 days', 'now');
            $inicio = $creado->toDateString();
            $fin = $creado->copy()->addDays(random_int(1, 12));
            $prioridad = [1, 2, 2, 3, 3, 3][random_int(0, 5)];
            $subtareas = random_int(0, 4);

            $filas[] = [
                'token' => bin2hex(random_bytes(6)),
                'quien' => 'despacho',
                'cuando' => $creado,
                'titulo' => $tipo . ' - OT ' . $ot,
                'prioridad' => $prioridad,
                'tarea' => $tipo,
                'abreviado' => mb_substr($tipo, 0, 3),
                'sub_tarea' => $subtareas ? 'Verificar instalacion #' . random_int(10, 99) : '',
                'sub_estado' => $subtareas ? DemoCatalog::rand(['Pendiente', 'Pendiente', 'Completada']) : '',
                'descripcion' => sprintf(
                    '%s en %s. Cliente: %s. OT %d. Coordinar con el cliente antes de salir. Zona: %s.',
                    $tipo, DemoCatalog::rand(DemoCatalog::LOCALIDADES), $cliente, $ot, $zona
                ),
                'archivo_uno' => '', 'nom_archivo_uno' => '',
                'archivo_dos' => '', 'nom_archivo_dos' => '',
                'archivo_tres' => '', 'nom_archivo_tres' => '',
                'inicio' => $inicio,
                'fin' => $fin->toDateString(),
                'a_quien' => $tecnico,
                'mensaje' => DemoCatalog::rand([
                    'Cliente confirmo presencia en el domicilio.',
                    'Falta material: se pidio al deposito.',
                    'Reagendado por lluvia.',
                    'Equipo retirado, pendiente prueba de velocidad.',
                    'Se coordino para el turno manana por la manana.',
                ]),
                'color' => self::COLORES[$estado],
                'estado' => $estado,
            ];
        }

        foreach (array_chunk($filas, 100) as $chunk) {
            Tarea::insert($chunk);
        }
    }
}
