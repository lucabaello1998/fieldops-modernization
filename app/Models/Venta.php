<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `ventas` (19 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class Venta extends Model
{
    protected $table = 'ventas';
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';

    protected $casts = [
            'id' => 'integer',
            'fecha' => 'date',
            'start' => 'datetime',
            'end' => 'datetime',
    ];
}
