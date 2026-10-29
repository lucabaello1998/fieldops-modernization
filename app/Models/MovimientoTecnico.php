<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `movimiento_tecnico` (9 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class MovimientoTecnico extends Model
{
    protected $table = 'movimiento_tecnico';

    public $timestamps = false;

    protected $guarded = [];

    protected $primaryKey = 'id';

    protected $casts = [
        'id' => 'integer',
        'cuando' => 'datetime',
    ];
}
