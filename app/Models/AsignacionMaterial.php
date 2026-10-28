<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `asignacion_material` (16 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class AsignacionMaterial extends Model
{
    protected $table = 'asignacion_material';
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';

    protected $casts = [
            'id' => 'integer',
            'cuando' => 'datetime',
            'fecha' => 'date',
            'sap' => 'integer',
            'cantidad' => 'integer',
            'ot' => 'integer',
    ];
}
