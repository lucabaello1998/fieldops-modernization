<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `descuentos` (10 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class Descuento extends Model
{
    protected $table = 'descuentos';
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';

    protected $casts = [
            'id' => 'integer',
            'fecha' => 'date',
            'ot' => 'integer',
            'monto' => 'integer',
    ];
}
