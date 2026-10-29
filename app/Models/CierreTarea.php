<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `cierre_tarea` (18 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class CierreTarea extends Model
{
    protected $table = 'cierre_tarea';

    public $timestamps = false;

    protected $guarded = [];

    protected $primaryKey = 'id';

    protected $casts = [
        'id' => 'integer',
        'cuando' => 'datetime',
        'fecha' => 'date',
    ];
}
