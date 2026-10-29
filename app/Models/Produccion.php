<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `produccion` (24 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class Produccion extends Model
{
    protected $table = 'produccion';

    public $timestamps = false;

    protected $guarded = [];

    protected $primaryKey = 'id';

    protected $casts = [
        'id' => 'integer',
        'fecha' => 'date',
        'horadep' => 'string',
        'horatarea' => 'string',
        'dosplay' => 'integer',
        'tresplay' => 'integer',
        'stb' => 'integer',
        'mudanza' => 'integer',
        'tcumplida' => 'integer',
        'tareasmtto' => 'integer',
        'bajas' => 'integer',
        'bajatec' => 'integer',
        'fin' => 'string',
        'mtto_int' => 'integer',
        'mtto_ext' => 'integer',
        'baja_desmonte' => 'integer',
        'mtto_reaco' => 'integer',
    ];
}
