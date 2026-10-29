<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `mensajes_tec` (12 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class MensajesTec extends Model
{
    protected $table = 'mensajes_tec';

    public $timestamps = false;

    protected $guarded = [];

    protected $primaryKey = 'id';

    protected $casts = [
        'id' => 'integer',
        'cuando' => 'datetime',
    ];
}
