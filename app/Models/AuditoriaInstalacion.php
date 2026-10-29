<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `auditoria_instalaciones` (25 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class AuditoriaInstalacion extends Model
{
    protected $table = 'auditoria_instalaciones';

    public $timestamps = false;

    protected $guarded = [];

    protected $primaryKey = 'id';

    protected $casts = [
        'id' => 'integer',
        'fecha_relevo' => 'datetime',
        'fecha' => 'date',
        'ot' => 'integer',
    ];
}
