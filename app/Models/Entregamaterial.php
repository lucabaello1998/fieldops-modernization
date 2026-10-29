<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `entregamaterial` (6 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class Entregamaterial extends Model
{
    protected $table = 'entregamaterial';

    public $timestamps = false;

    protected $guarded = [];

    protected $primaryKey = 'id';

    protected $casts = [
        'id' => 'integer',
        'fechaegre' => 'date',
        'cantidad' => 'integer',
    ];
}
