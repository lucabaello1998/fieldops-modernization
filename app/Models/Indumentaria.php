<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `indumentaria` (11 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class Indumentaria extends Model
{
    protected $table = 'indumentaria';

    public $timestamps = false;

    protected $guarded = [];

    protected $primaryKey = 'id';

    protected $casts = [
        'id' => 'integer',
        'cantidad' => 'integer',
        'fecha' => 'date',
        'fechaegre' => 'date',
        'cantec' => 'integer',
    ];
}
