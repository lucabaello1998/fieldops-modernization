<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `reincidencias` (21 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class Reincidencia extends Model
{
    protected $table = 'reincidencias';

    public $timestamps = false;

    protected $guarded = [];

    protected $primaryKey = 'id';

    protected $casts = [
        'id' => 'integer',
        'ot' => 'integer',
        'fechaint' => 'date',
        'fecharep' => 'date',
        'otdos' => 'integer',
    ];
}
