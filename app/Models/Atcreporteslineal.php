<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `atcreporteslineal` (22 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class Atcreporteslineal extends Model
{
    protected $table = 'atcreporteslineal';
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';

    protected $casts = [
            'id' => 'integer',
            'fecha' => 'date',
            'num_empleado' => 'integer',
    ];
}
