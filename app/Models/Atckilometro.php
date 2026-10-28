<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `atckilometros` (10 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class Atckilometro extends Model
{
    protected $table = 'atckilometros';
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';

    protected $casts = [
            'id' => 'integer',
            'reportes' => 'integer',
    ];
}
