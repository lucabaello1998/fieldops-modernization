<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `movimiento_interno` (13 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class MovimientoInterno extends Model
{
    protected $table = 'movimiento_interno';
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';

    protected $casts = [
            'id' => 'integer',
            'cuando' => 'datetime',
            'inicio' => 'date',
    ];
}
