<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `tareas` (23 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class Tarea extends Model
{
    protected $table = 'tareas';
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';

    protected $casts = [
            'id' => 'integer',
            'cuando' => 'datetime',
            'prioridad' => 'integer',
            'inicio' => 'date',
            'fin' => 'date',
    ];
}
