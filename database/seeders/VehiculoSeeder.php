<?php

namespace Database\Seeders;

use App\Models\Vehiculo;
use App\Support\DemoCatalog;
use Illuminate\Database\Seeder;

class VehiculoSeeder extends Seeder
{
    public function run(): void
    {
        $filas = [];
        $marcas = ['Toyota Hilux', 'Ford Ranger', 'Chevrolet S10', 'Fiat Toro', 'Renault Kangoo', 'Peugeot Partner', 'Volkswagen Amarok'];
        $colores = ['Blanco', 'Gris', 'Negro', 'Rojo', 'Azul'];

        for ($n = 0; $n < 46; $n++) {
            $vtv = DemoCatalog::fechaEntre('-10 months', '+14 months');
            $seguro = DemoCatalog::fechaEntre('-6 months', '+20 months');

            $filas[] = [
                'patente' => strtoupper(DemoCatalog::rand(['AB', 'AC', 'AD', 'AE', 'AF', 'AG'])) . random_int(100, 999) . strtoupper(DemoCatalog::rand(['CD', 'EF', 'GH', 'IJ'])),
                'auto' => DemoCatalog::rand($marcas),
                'color' => DemoCatalog::rand($colores),
                'vigente' => $vtv->isFuture() ? 'Si' : 'No',
                'vtv' => $vtv->toDateString(),
                'vigenteseg' => $seguro->isFuture() ? 'Si' : 'No',
                'seguro' => $seguro->toDateString(),
                'archivo' => '',
            ];
        }

        Vehiculo::insert($filas);
    }
}
