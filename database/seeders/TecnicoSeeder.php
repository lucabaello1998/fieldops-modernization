<?php

namespace Database\Seeders;

use App\Models\Tecnico;
use App\Support\DemoCatalog;
use Illuminate\Database\Seeder;

class TecnicoSeeder extends Seeder
{
    private const NOMBRES = [
        'Julian', 'Matias', 'Gabriela', 'Lorena', 'Rodrigo', 'Leandro', 'Camila', 'Daniela',
        'Pablo', 'Yamila', 'Facundo', 'Sergio', 'Carla', 'Roberto', 'Nestor', 'Paula',
        'Hernan', 'Analia', 'Vanina', 'Emiliano', 'Mariana', 'Gaston', 'Silvana', 'Diego',
        'Nicolas', 'Alejandro', 'Romina', 'Veronica', 'Cristian', 'Mirta', 'Jose', 'Agustin',
    ];

    private const APELLIDOS = [
        'Ropero', 'Molina', 'Duarte', 'Gimenez', 'Perez', 'Oliva', 'Sosa', 'Ibarra',
        'Ayala', 'Funes', 'Nieva', 'Vega', 'Coronel', 'Aguirre', 'Bustos', 'Quiroga',
        'Cardozo', 'Ruiz', 'Paz', 'Villalba', 'Ferreyra', 'Ledesma', 'Barrionuevo', 'Farias',
        'Cabrera', 'Nunez', 'Britez', 'Zarate', 'Escobar', 'Godoy', 'Rios', 'Salcedo',
    ];

    private const MARCAS = ['Toyota Hilux', 'Ford Ranger', 'Chevrolet S10', 'Fiat Toro', 'Renault Kangoo', 'Peugeot Partner'];

    public function run(): void
    {
        // Pares nombre+apellido unicos: se barajan las 1.024 combinaciones posibles con el
        // RNG ya sembrado por DatabaseSeeder (mt_srand), asi el plantel no repite personas.
        $combos = [];
        foreach (self::NOMBRES as $n) {
            foreach (self::APELLIDOS as $a) {
                $combos[] = [$n, $a];
            }
        }
        for ($i = count($combos) - 1; $i > 0; $i--) {
            $j = mt_rand(0, $i);
            [$combos[$i], $combos[$j]] = [$combos[$j], $combos[$i]];
        }

        $filas = [];
        $usados = [];

        for ($i = 1; $i <= 142; $i++) {
            [$nombre, $apellido] = $combos[$i - 1];

            // El handle tiene que ser unico: en el legacy es la clave de relacion por texto.
            $base = mb_strtolower(mb_substr($nombre, 0, 1) . $apellido);
            $usuario = $base;
            $sufijo = 2;
            while (isset($usados[$usuario])) {
                $usuario = $base . $sufijo++;
            }
            $usados[$usuario] = true;

            $zona = DemoCatalog::rand(DemoCatalog::ZONAS);
            $activo = $i % 11 !== 0;

            $filas[] = [
                'token' => bin2hex(random_bytes(6)),
                'tecnico' => $usuario,
                'nombre' => $nombre,
                'apellido' => $apellido,
                'dni' => random_int(24000000, 44999999),
                'id_recurso' => 1000 + $i,
                'chomba' => random_int(0, 3) === 0 ? '' : ['S', 'M', 'L', 'XL'][random_int(0, 3)],
                'pantalon' => ['38', '40', '42', '44', '46'][random_int(0, 4)],
                'zapato' => ['39', '40', '41', '42', '43', '44'][random_int(0, 5)],
                'ingreso' => now()->subDays(random_int(30, 2600))->toDateString(),
                'modelo' => DemoCatalog::rand(self::MARCAS),
                'sn' => strtoupper(bin2hex(random_bytes(4))),
                'flota' => (string) random_int(100, 999),
                'tel' => '11' . random_int(40000000, 79999999),
                'mail' => $usuario . '@fieldops.test',
                'patente' => strtoupper(DemoCatalog::rand(['AB', 'AC', 'AD', 'AE', 'AF'])) . random_int(100, 999) . strtoupper(DemoCatalog::rand(['CD', 'EF', 'GH'])),
                'tusu' => 'fiberhome',
                'tcon' => (string) random_int(100000, 999999),
                'sgtusu' => 'hw',
                'sgtcon' => (string) random_int(100000, 999999),
                'activo' => $activo ? 'Si' : 'No',
                'zona' => $zona,
                'tipo' => DemoCatalog::rand(['Planta externa', 'Instalador', 'Mantenimiento', 'Instalador']),
                'deposito' => DemoCatalog::rand(DemoCatalog::DEPOSITOS),
            ];
        }

        foreach (array_chunk($filas, 100) as $chunk) {
            Tecnico::insert($chunk);
        }
    }
}
