<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\DemoCatalog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Usuarios de la demo. Password hasheada con bcrypt.
 */
class UserSeeder extends Seeder
{
    public function run(): void
    {
        $demo = [
            ['Luca Baello', 'admin@fieldops.test', 'Administrador', 'CABA Norte'],
            ['Marta Sosa', 'supervisor@fieldops.test', 'Supervisor', 'CABA Sur'],
            ['Julian Perez', 'tecnico@fieldops.test', 'Tecnico', 'GBA Norte'],
            ['Diego Ferreyra', 'deposito@fieldops.test', 'Deposito', 'Central'],
            ['Sofia Ledesma', 'despacho@fieldops.test', 'Despacho', 'CABA Centro'],
        ];

        foreach ($demo as [$nombre, $mail, $rol, $zona]) {
            User::updateOrCreate(
                ['email' => $mail],
                [
                    'name' => $nombre,
                    'password' => 'password',
                    'role' => $rol,
                    'activo' => true,
                    'zona' => $zona,
                    'abreviatura' => mb_strtoupper(mb_substr($nombre, 0, 1) . mb_substr(explode(' ', $nombre)[1] ?? '', 0, 1)),
                    'ultimo_acceso' => now()->subHours(random_int(1, 900)),
                    'email_verified_at' => now(),
                ]
            );
        }

        // Mas usuarios de staff para que el modulo de usuarios tenga volumen.
        $nombres = [
            'Roberto Gimenez', 'Carla Duarte', 'Nestor Villalba', 'Paula Bustos', 'Hernan Quiroga',
            'Analia Cardozo', 'Facundo Ropero', 'Vanina Ruiz', 'Matias Oliva', 'Gabriela Sosa',
            'Emiliano Paz', 'Lorena Ibarra', 'Rodrigo Ayala', 'Mariana Correa', 'Leandro Funes',
            'Camila Nieva', 'Sergio Barrionuevo', 'Daniela Vega', 'Pablo Coronel', 'Yamila Aguirre',
        ];

        foreach ($nombres as $i => $nombre) {
            User::updateOrCreate(
                ['email' => sprintf('%s.%s@fieldops.test', mb_strtolower(explode(' ', $nombre)[0]), mb_strtolower(explode(' ', $nombre)[1]))],
                [
                    'name' => $nombre,
                    'password' => Hash::make('password'),
                    'role' => DemoCatalog::rand(['Supervisor', 'Tecnico', 'Tecnico', 'Deposito', 'Despacho']),
                    'activo' => $i % 9 !== 0,
                    'zona' => DemoCatalog::rand(DemoCatalog::ZONAS),
                    'ultimo_acceso' => now()->subDays(random_int(0, 120)),
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
