<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `reclamos` (18 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class Reclamo extends Model
{
    protected $table = 'reclamos';
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';

    protected $casts = [
            'id' => 'integer',
            'ot' => 'integer',
            'fechains' => 'date',
            'fechamail' => 'date',
            'telefono' => 'integer',
            'fechasolu' => 'date',
            'gasto' => 'integer',
    ];
}
