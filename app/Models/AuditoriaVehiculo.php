<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `auditoria_vehiculo` (116 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class AuditoriaVehiculo extends Model
{
    protected $table = 'auditoria_vehiculo';

    public $timestamps = false;

    protected $guarded = [];

    protected $primaryKey = 'id';

    protected $casts = [
        'id' => 'integer',
        'fecha_relevo' => 'datetime',
        'fecha' => 'date',
    ];
}
