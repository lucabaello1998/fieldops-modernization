<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `cambiotecnologia` (13 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class Cambiotecnologia extends Model
{
    protected $table = 'cambiotecnologia';
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';

    protected $casts = [
            'id' => 'integer',
            'ot' => 'integer',
            'calendario' => 'date',
    ];
}
