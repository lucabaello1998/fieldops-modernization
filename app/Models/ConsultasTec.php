<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `consultas_tec` (9 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class ConsultasTec extends Model
{
    protected $table = 'consultas_tec';
    public $timestamps = false;
    protected $guarded = [];
    protected $primaryKey = 'id';

    protected $casts = [
            'id' => 'integer',
            'cuando' => 'datetime',
    ];
}
