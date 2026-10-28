<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `tecnicos` (25 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class Tecnico extends Model
{
    protected $table = 'tecnicos';
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';

    protected $casts = [
            'id' => 'integer',
            'dni' => 'integer',
            'id_recurso' => 'integer',
            'ingreso' => 'date',
    ];
}
