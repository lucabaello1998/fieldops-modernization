<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `entregaindumentaria` (7 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class Entregaindumentaria extends Model
{
    protected $table = 'entregaindumentaria';
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';

    protected $casts = [
            'id' => 'integer',
            'fechaegre' => 'date',
            'cantidad' => 'integer',
    ];
}
