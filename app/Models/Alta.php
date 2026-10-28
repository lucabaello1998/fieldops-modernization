<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `altas` (31 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class Alta extends Model
{
    protected $table = 'altas';
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';

    protected $casts = [
            'id' => 'integer',
            'cuando' => 'datetime',
            'ot' => 'integer',
            'calendario' => 'date',
    ];
}
