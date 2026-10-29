<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `tecnicosatc` (15 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class Tecnicosatc extends Model
{
    protected $table = 'tecnicosatc';

    public $timestamps = false;

    protected $guarded = [];

    protected $primaryKey = 'id';

    protected $casts = [
        'id' => 'integer',
        'num_empleado' => 'integer',
        'dni' => 'integer',
        'inicio' => 'date',
        'fin' => 'date',
    ];
}
