<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabla legacy `final` (18 columnas) - mapeo directo del esquema real.
 * Generado por tools/gen_models.py
 */
class FinalRegistro extends Model
{
    protected $table = 'final';

    public $timestamps = false;

    protected $guarded = [];

    protected $primaryKey = null;

    public $incrementing = false;

    protected $casts = [
        'UB DESTINO' => 'integer',
    ];
}
