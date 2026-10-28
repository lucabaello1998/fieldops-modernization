<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `devolucion` (26 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class Devolucion extends Model
{
    protected $table = 'devolucion';
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';

    protected $casts = [
            'id' => 'integer',
            'cuando' => 'datetime',
            'fecha' => 'date',
            'sap' => 'integer',
            'cantidad' => 'integer',
            'fecha_retiro' => 'date',
            'num_remito' => 'integer',
            'legajo' => 'integer',
            'pallet' => 'integer',
            'num_caja' => 'integer',
    ];
}
