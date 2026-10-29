<?php

namespace App\Support;

use Illuminate\Support\Carbon;

/**
 * Catalogos de datos SINTETICOS para la demo.
 *
 * Nada de esto son datos reales de produccion: son valores plausibles
 * (zonas, materiales de telecomunicaciones, motivos) para poblar la app.
 */
class DemoCatalog
{
    public const ZONAS = [
        'CABA Norte', 'CABA Sur', 'CABA Centro', 'GBA Norte', 'GBA Sur', 'GBA Oeste',
        'La Plata', 'Mar del Plata', 'Rosario', 'Cordoba', 'Mendoza', 'Tucuman',
        'Santa Fe', 'Salta', 'Neuquen', 'Bahia Blanca',
    ];

    public const DEPOSITOS = ['Central', 'Norte', 'Sur', 'Oeste', 'La Plata', 'Mar del Plata'];

    public const LOCALIDADES = [
        'Vicente Lopez', 'San Isidro', 'Tigre', 'San Martin', 'Tres de Febrero', 'Moron',
        'La Matanza', 'Lanus', 'Avellaneda', 'Quilmes', 'Berazategui', 'Lomas de Zamora',
        'La Plata', 'Ensenada', 'Berisso', 'Pilar', 'Escobar', 'San Miguel',
    ];

    public const ACTIVIDADES = [
        'Instalacion', 'Reclamo', 'Mantenimiento', 'Baja', 'Reparacion', 'Reincidencia',
        'Cambio de tecnologia', 'Mudanza', 'Garantia',
    ];

    public const MOTIVOS_BAJA = [
        'Mudanza', 'Solicitud del cliente', 'Falta de pago', 'Cambio de tecnologia',
        'Reincidencia tecnica', 'Duplicado administrativo', 'Zona sin cobertura',
    ];

    /** Codigo SAP => descripcion del material */
    public const MATERIALES = [
        '100000001' => 'ONT Huawei HG8010H GPON',
        '100000002' => 'ONT Huawei HG8145V5 dual band',
        '100000003' => 'ONT ZTE F670L GPON',
        '100000004' => 'Decodificador IPTV Arris 4K',
        '100000005' => 'Decodificador HD DVC-1000',
        '100000006' => 'Cable drop optico 100 m 2 hilos',
        '100000007' => 'Cable drop optico 200 m 2 hilos',
        '100000008' => 'Splitter optico 1x8 SC/APC',
        '100000009' => 'Splitter optico 1x4 SC/APC',
        '100000010' => 'Roseta optica SC/APC 2 puertos',
        '100000011' => 'Conector SC/APC de campo',
        '100000012' => 'Pigtail SC/APC 1 metro',
        '100000013' => 'Caja de paso optica 8 puertos',
        '100000014' => 'Fusionadora de fibra optica',
        '100000015' => 'Powerline TP-Link AV1000',
        '100000016' => 'Access point WiFi 6 AX1800',
        '100000017' => 'Router neutro AC1200',
        '100000018' => 'Cable UTP categoria 6 (rollo 305 m)',
        '100000019' => 'Cinta aisladora 20 m',
        '100000020' => 'Grampa para drop optico (x100)',
        '100000021' => 'Abrazadera metalica 3/8',
        '100000022' => 'Morseto anclaje para acometida',
        '100000023' => 'Caja de conexion IP65',
        '100000024' => 'Prolongador electrico 10 m',
        '100000025' => 'Alargue de fibra SC/APC 20 m',
        '100000026' => 'Muslera porta material',
        '100000027' => 'Escalera de fibra 8 escalones',
        '100000028' => 'Casco de seguridad con barbijo',
        '100000029' => 'Arnes de seguridad 4 puntos',
        '100000030' => 'Guante dielectrico clase 2',
        '100000031' => 'Linga de acero 1 m',
        '100000032' => 'Alcohol isopropilico 500 ml',
        '100000033' => 'Lapiz limpiador de fibra',
        '100000034' => 'Paleta de enduido 200 g',
        '100000035' => 'Silicona neutra transparente',
        '100000036' => 'Tornillo autoperforante tirafondo (x50)',
        '100000037' => 'Precinto plastico 300 mm (x100)',
        '100000038' => 'Bateria de respaldo 12V 7Ah',
        '100000039' => 'Fuente switching 12V 2A',
        '100000040' => 'Bolso kit instalador',
    ];

    public const TIPOS_TAREA = [
        'Instalacion de servicio', 'Reclamo tecnico', 'Mantenimiento preventivo',
        'Baja de servicio', 'Cambio de equipo', 'Auditoria de instalacion',
        'Reparacion de red', 'Visita comercial',
    ];

    public static function material(int $sap): string
    {
        return self::MATERIALES[(string) $sap] ?? "Material {$sap}";
    }

    public static function rand(array $items)
    {
        return $items[array_rand($items)];
    }

    /** Fecha aleatoria entre $desde y $hasta (Carbon). */
    public static function fechaEntre(string $desde, string $hasta)
    {
        return Carbon::createFromTimestamp(
            random_int(strtotime($desde), strtotime($hasta))
        );
    }
}
