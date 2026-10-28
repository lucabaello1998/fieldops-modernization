<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `asistenciaayudantes` (5 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class Asistenciaayudant extends Model
{
    protected $table = 'asistenciaayudantes';
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';

    protected $casts = [
            'id' => 'integer',
            'fecha' => 'date',
    ];
}
