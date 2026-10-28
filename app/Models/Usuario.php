<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `usuarios` (16 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class Usuario extends Model
{
    protected $table = 'usuarios';
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';

    protected $casts = [
            'id' => 'integer',
            'ingreso' => 'date',
    ];
}
