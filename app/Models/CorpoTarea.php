<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `corpo_tareas` (17 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class CorpoTarea extends Model
{
    protected $table = 'corpo_tareas';

    public $timestamps = false;

    protected $guarded = [];

    protected $primaryKey = 'id';

    protected $casts = [
        'id' => 'integer',
        'F_Asignado' => 'date',
        'orden' => 'integer',
        'enlace' => 'integer',
    ];
}
