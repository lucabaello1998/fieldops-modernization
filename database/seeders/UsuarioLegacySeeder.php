<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;

/**
 * La tabla legacy `usuarios`: password EN TEXTO PLANO.
 *
 * Estos son usuarios SINTETICOS, no los 96 reales que se extrajeron de
 * produccion. Se cargan (con una marca de advertencia) para poder mostrar
 * en la demo el contraste "antes vs despues" del manejo de credenciales.
 */
class UsuarioLegacySeeder extends Seeder
{
    public function run(): void
    {
        // En la tabla real de produccion esto es `pass` sin hashear.
        $usuarios = [
            ['Ropero', 'Facundo', 'Administrador'], ['Molina', 'Sergio', 'Administrador'],
            ['Duarte', 'Carla', 'Supervisor'], ['Gimenez', 'Roberto', 'Supervisor'],
            ['Perez', 'Julian', 'Tecnico'], ['Oliva', 'Matias', 'Tecnico'],
            ['Sosa', 'Gabriela', 'Tecnico'], ['Ibarra', 'Lorena', 'Tecnico'],
            ['Ayala', 'Rodrigo', 'Tecnico'], ['Funes', 'Leandro', 'Tecnico'],
            ['Nieva', 'Camila', 'Tecnico'], ['Vega', 'Daniela', 'Tecnico'],
            ['Coronel', 'Pablo', 'Tecnico'], ['Aguirre', 'Yamila', 'Tecnico'],
            ['Bustos', 'Paula', 'Deposito'], ['Quiroga', 'Hernan', 'Deposito'],
            ['Cardozo', 'Analia', 'Despacho'], ['Ruiz', 'Vanina', 'Despacho'],
            ['Paz', 'Emiliano', 'Supervisor'], ['Villalba', 'Nestor', 'Administrador'],
        ];

        $filas = [];
        foreach ($usuarios as $i => [$apellido, $nombre, $tipo]) {
            $usuario = mb_strtolower(mb_substr($nombre, 0, 1).$apellido);
            $filas[] = [
                'token' => bin2hex(random_bytes(8)),
                'ingreso' => now()->subDays(random_int(60, 1200))->toDateString(),
                'activo' => 'Si',
                'nombre' => $nombre,
                'apellido' => $apellido,
                'mail' => $usuario.'@legacy.local',
                'usuario' => $usuario,
                'pass' => $nombre,          // columna del esquema original, sin hash
                'tipo_us' => $tipo,
                'zona' => 'CABA Norte',
                'abreviatura' => mb_strtoupper(mb_substr($nombre, 0, 1).mb_substr($apellido, 0, 1)),
                'icono' => 'user',
                'tema' => 'claro',
                'fuente' => 'Inter',
                'firebase' => '',
            ];
        }

        Usuario::insert($filas);
    }
}
